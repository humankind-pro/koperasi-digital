from flask import Flask, request, jsonify
from flask_cors import CORS
import requests
import re
import os
import json
from datetime import datetime
import traceback
from dotenv import load_dotenv
import base64

# Load environment variables
load_dotenv()

app = Flask(__name__)
CORS(app)

# API KEY - PENTING: Ganti dengan API key Anda yang valid!
API_KEY = os.getenv('OCR_API_KEY', 'K89874851988957')

# MODE LOGGING
DEBUG_MODE = os.getenv('OCR_DEBUG', 'true').lower() == 'true'

def log_debug(message, data=None):
    """Logging yang bisa di-toggle on/off"""
    if DEBUG_MODE:
        print(message)
        if data:
            print(json.dumps(data, indent=2) if isinstance(data, dict) else data)

def log_info(message):
    """Logging penting yang selalu tampil"""
    print(message)

@app.route('/extract-ktp', methods=['POST'])
def extract_ktp():
    temp_path = None
    
    try:
        log_info("\n" + "="*60)
        log_info("NEW REQUEST")
        log_info("="*60)
        
        if 'file' not in request.files:
            log_info("ERROR: No file")
            return jsonify({'success': False, 'message': 'No file'}), 400
        
        file = request.files['file']
        log_info(f"File: {file.filename}")
        
        # Buat folder temp
        temp_dir = os.path.join(os.path.dirname(__file__), 'temp')
        os.makedirs(temp_dir, exist_ok=True)
        
        # Simpan temporary
        temp_filename = f"temp_{datetime.now().strftime('%Y%m%d%H%M%S')}.jpg"
        temp_path = os.path.join(temp_dir, temp_filename)
        file.save(temp_path)
        
        file_size = os.path.getsize(temp_path)
        log_info(f"Size: {file_size} bytes ({file_size/1024:.2f} KB)")
        log_debug(f"Saved: {temp_path}")
        
        # ✅ PERBAIKAN: Kirim ke OCR.Space dengan method yang BENAR
        log_info("Sending to OCR.Space...")
        log_info(f"Using API Key: {API_KEY[:10]}...")
        
        # Method 1: Multipart form-data (RECOMMENDED)
        with open(temp_path, 'rb') as f:
            payload = {
                'apikey': API_KEY,
                'language': 'eng',
                'OCREngine': '2',
                'isTable': 'true',
                'scale': 'true',
                'detectOrientation': 'true'
            }
            
            files_data = {
                'filename': (file.filename, f, 'image/jpeg')
            }
            
            log_debug(f"Payload: {payload}")
            
            # ✅ URL YANG BENAR - TANPA SPASI!
            url = 'https://api.ocr.space/parse/image'
            log_debug(f"URL: {url}")
            
            try:
                response = requests.post(
                    url,
                    files=files_data,
                    data=payload,
                    timeout=60
                )
            except requests.exceptions.ConnectionError as e:
                log_info(f"Connection Error: {str(e)}")
                return jsonify({'success': False, 'message': 'Tidak bisa terhubung ke OCR service'}), 503
        
        log_info(f"Response status: {response.status_code}")
        log_debug(f"Response headers: {dict(response.headers)}")
        
        # Hapus file temp
        cleanup_temp_file(temp_path)
        temp_path = None
        
        # ✅ HANDLE BERBAGAI STATUS CODE
        if response.status_code == 404:
            log_info("ERROR 404: Endpoint not found atau API key invalid!")
            log_info("SOLUSI:")
            log_info("1. Pastikan API key Anda valid")
            log_info("2. Daftar API key gratis di: https://ocr.space/ocrapi")
            log_info("3. Cek response body:")
            log_info(response.text[:500])
            return jsonify({
                'success': False, 
                'message': 'OCR API key tidak valid atau endpoint salah. Silakan daftar di https://ocr.space/ocrapi'
            }), 400
        
        if response.status_code == 403:
            log_info("ERROR 403: API key forbidden atau rate limit exceeded")
            return jsonify({
                'success': False,
                'message': 'API key tidak valid atau rate limit tercapai'
            }), 403
        
        if response.status_code != 200:
            log_info(f"ERROR: Status {response.status_code}")
            log_info(f"Response: {response.text[:500]}")
            return jsonify({
                'success': False, 
                'message': f'OCR service error (Status: {response.status_code})'
            }), 500
        
        # Parse JSON response
        try:
            result = response.json()
        except json.JSONDecodeError:
            log_info("ERROR: Invalid JSON response")
            log_info(f"Response text: {response.text[:500]}")
            return jsonify({'success': False, 'message': 'Invalid response from OCR service'}), 500
        
        log_debug("Full OCR Response:", result)
        
        # Check processing errors
        if result.get("IsErroredOnProcessing"):
            error_msg = result.get("ErrorMessage", "OCR Error")
            if isinstance(error_msg, list):
                error_msg = error_msg[0] if error_msg else "Unknown error"
            log_info(f"OCR Processing Error: {error_msg}")
            return jsonify({'success': False, 'message': str(error_msg)}), 500
        
        # Get parsed results
        parsed_results = result.get("ParsedResults")
        if not parsed_results or len(parsed_results) == 0:
            log_info("No ParsedResults in response")
            return jsonify({'success': False, 'message': 'No text detected'}), 400
        
        raw_text = parsed_results[0].get("ParsedText", "")
        log_info(f"Text length: {len(raw_text)}")
        log_debug(f"Raw text preview:\n{raw_text[:500]}...")
        
        if len(raw_text) < 50:
            log_info("Text too short - foto tidak terbaca dengan baik")
            return jsonify({'success': False, 'message': 'Foto tidak terbaca dengan baik'}), 400
        
        # Normalisasi text
        normalized_text = normalize_text(raw_text)
        
        # Ekstrak data KTP
        extracted_data = extract_ktp_data(normalized_text)
        
        # Validasi hasil
        validation_errors = validate_extracted_data(extracted_data)
        if validation_errors:
            log_info(f"Validation errors: {validation_errors}")
            return jsonify({
                'success': False,
                'message': 'Data KTP tidak valid: ' + ', '.join(validation_errors),
                'raw_text': raw_text if DEBUG_MODE else None
            }), 400
        
        log_info(f"\n✅ BERHASIL EKSTRAK DATA:")
        log_info(f"   NIK: {extracted_data.get('nik')}")
        log_info(f"   Nama: {extracted_data.get('nama')}")
        log_info(f"   Tempat Lahir: {extracted_data.get('tempat_lahir')}")
        log_info(f"   Tanggal Lahir: {extracted_data.get('tanggal_lahir')}")
        log_info(f"   Jenis Kelamin: {extracted_data.get('jenis_kelamin')}")
        log_info(f"   Agama: {extracted_data.get('agama')}")
        log_info(f"   Pekerjaan: {extracted_data.get('pekerjaan')}")
        
        return jsonify({
            'success': True,
            'data': extracted_data,
            'raw_text': raw_text if DEBUG_MODE else None
        })
    
    except requests.exceptions.Timeout:
        log_info("ERROR: Request timeout")
        return jsonify({'success': False, 'message': 'OCR request timeout'}), 504
    
    except requests.exceptions.RequestException as e:
        log_info(f"ERROR: Request exception - {str(e)}")
        return jsonify({'success': False, 'message': 'Failed to connect to OCR service'}), 503
    
    except Exception as e:
        log_info("\n" + "="*60)
        log_info("EXCEPTION!")
        log_info("="*60)
        log_info(f"Error: {str(e)}")
        log_debug(traceback.format_exc())
        log_info("="*60)
        return jsonify({'success': False, 'message': 'Internal server error'}), 500
    
    finally:
        if temp_path and os.path.exists(temp_path):
            cleanup_temp_file(temp_path)

def cleanup_temp_file(file_path):
    """Hapus file temporary dengan aman"""
    try:
        if os.path.exists(file_path):
            os.remove(file_path)
            log_debug(f"✅ Temp file deleted: {file_path}")
    except Exception as e:
        log_debug(f"⚠️ Failed to delete temp file: {e}")

def normalize_text(text):
    """Normalisasi text OCR"""
    text = re.sub(r'[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]', '', text)
    text = re.sub(r'\s+', ' ', text)
    text = re.sub(r' ?\n ?', '\n', text)
    text = text.replace('|', 'I')
    text = text.replace('0', 'O', 1) if text.startswith('0') else text
    text = re.sub(r'\s*:\s*', ': ', text)
    text = re.sub(r'\s*/\s*', '/', text)
    text = text.replace('Tempal', 'Tempat')
    text = text.replace('Tgi', 'Tgl')
    text = text.replace('Lahir ', 'Lahir: ')
    return text.strip()

def validate_extracted_data(data):
    """Validasi hasil ekstraksi"""
    errors = []
    
    nik = data.get('nik', '')
    if not nik:
        errors.append("NIK tidak terbaca")
    elif len(nik) != 16:
        errors.append(f"NIK harus 16 digit (terbaca: {len(nik)} digit)")
    elif not nik.isdigit():
        errors.append("NIK harus berisi angka saja")
    
    nama = data.get('nama', '')
    if not nama:
        errors.append("Nama tidak terbaca")
    elif len(nama) < 3:
        errors.append("Nama terlalu pendek")
    
    tanggal_lahir = data.get('tanggal_lahir', '')
    if tanggal_lahir:
        if not re.match(r'\d{2}-\d{2}-\d{4}', tanggal_lahir):
            errors.append("Format tanggal lahir tidak valid (harus DD-MM-YYYY)")
        else:
            try:
                day, month, year = map(int, tanggal_lahir.split('-'))
                if not (1 <= day <= 31 and 1 <= month <= 12 and 1900 <= year <= datetime.now().year):
                    errors.append("Tanggal lahir tidak valid")
            except:
                errors.append("Tanggal lahir tidak bisa diparse")
    
    return errors

def extract_ktp_data(text):
    """Ekstrak data dari teks KTP"""
    data = {
        'nik': '',
        'nama': '',
        'tempat_lahir': '',
        'tanggal_lahir': '',
        'jenis_kelamin': '',
        'gol_darah': '',
        'alamat': '',
        'rt': '',
        'rw': '',
        'kelurahan': '',
        'desa': '',
        'kecamatan': '',
        'kabupaten_kota': '',
        'provinsi': '',
        'agama': '',
        'status_perkawinan': '',
        'status': '',
        'pekerjaan': '',
        'kewarganegaraan': ''
    }
    
    text_upper = text.upper()
    
    # 1. NIK (16 digit)
    nik_patterns = [
        r'NIK\s*:?\s*(\d{16})',
        r'N\.?I\.?K\.?\s*:?\s*(\d{16})',
        r'\b(\d{16})\b'
    ]
    for pattern in nik_patterns:
        nik_match = re.search(pattern, text_upper)
        if nik_match:
            data['nik'] = nik_match.group(1)
            break
    
    # 2. Nama
    nama_patterns = [
        r'NAMA\s*:?\s*([A-Z][A-Z\s\.]+?)(?=\s*\n|\s*TEMPAT|\s*TTL)',
        r'N\.?A\.?M\.?A\s*:?\s*([A-Z][A-Z\s\.]+?)(?=\s*\n|\s*TEMPAT)'
    ]
    for pattern in nama_patterns:
        nama_match = re.search(pattern, text_upper)
        if nama_match:
            data['nama'] = nama_match.group(1).strip()
            break
    
    # 3. Tempat/Tanggal Lahir
    ttl_patterns = [
        r'(?:TEMPAT|TEMPAL).*?(?:TGL|TANGGAL)?\s*LAHIR\s*:?\s*([A-Z][A-Z\s]+?),\s*(\d{2}[-/]\d{2}[-/]\d{4})',
        r'TTL\s*:?\s*([A-Z][A-Z\s]+?),\s*(\d{2}[-/]\d{2}[-/]\d{4})',
        r'LAHIR\s*:?\s*([A-Z][A-Z\s]+?),\s*(\d{2}[-/]\d{2}[-/]\d{4})'
    ]
    for pattern in ttl_patterns:
        ttl_match = re.search(pattern, text_upper)
        if ttl_match:
            data['tempat_lahir'] = ttl_match.group(1).strip()
            data['tanggal_lahir'] = ttl_match.group(2).replace('/', '-')
            break
    
    # 4. Jenis Kelamin
    jk_patterns = [
        r'JENIS\s*KELAMIN\s*:?\s*(LAKI[-\s]?LAKI|PEREMPUAN)',
        r'JK\s*:?\s*(LAKI[-\s]?LAKI|PEREMPUAN|L|P)',
        r'KELAMIN\s*:?\s*(LAKI[-\s]?LAKI|PEREMPUAN|L|P)'
    ]
    for pattern in jk_patterns:
        jk_match = re.search(pattern, text_upper)
        if jk_match:
            jk = jk_match.group(1).strip().replace(' ', '-')
            data['jenis_kelamin'] = jk
            break
    
    # 5. Golongan Darah
    gol_patterns = [
        r'GOL\.?\s*DARAH\s*:?\s*([ABO]|AB)',
        r'DARAH\s*:?\s*([ABO]|AB)',
        r'GOL[:\s]+([ABO]|AB)'
    ]
    for pattern in gol_patterns:
        gol_match = re.search(pattern, text_upper)
        if gol_match:
            data['gol_darah'] = gol_match.group(1).strip()
            break
    
    # 6. Alamat
    alamat_match = re.search(r'ALAMAT\s*:?\s*([^\n]+)', text_upper)
    if alamat_match:
        alamat_raw = alamat_match.group(1).strip()
        alamat_raw = re.split(r'\s*RT[/\s]*RW', alamat_raw)[0].strip()
        data['alamat'] = alamat_raw
    
    # 7. RT/RW
    rt_rw_patterns = [
        r'RT[/\s]*RW\s*:?\s*(\d{1,3})\s*/\s*(\d{1,3})',
        r'RT\s*:?\s*(\d{1,3})\s*RW\s*:?\s*(\d{1,3})',
        r'(\d{3})\s*/\s*(\d{3})'
    ]
    for pattern in rt_rw_patterns:
        rt_rw_match = re.search(pattern, text_upper)
        if rt_rw_match:
            data['rt'] = rt_rw_match.group(1).zfill(3)
            data['rw'] = rt_rw_match.group(2).zfill(3)
            break
    
    # 8. Kelurahan/Desa
    kel_patterns = [
        r'(?:KELURAHAN|DESA|KEL[/\s]*DESA)\s*:?\s*([A-Z\s]+?)(?=\s*\n|\s*KECAMATAN)',
        r'KEL\.\s*:?\s*([A-Z\s]+?)(?=\s*\n|\s*KEC)'
    ]
    for pattern in kel_patterns:
        kel_match = re.search(pattern, text_upper, re.IGNORECASE)
        if kel_match:
            kel_value = kel_match.group(1).strip()
            data['kelurahan'] = kel_value
            data['desa'] = kel_value
            break
    
    # 9. Kecamatan
    kec_patterns = [
        r'KECAMATAN\s*:?\s*([A-Z\s]+?)(?=\s*\n|\s*AGAMA|\s*KABUPATEN|\s*KOTA)',
        r'KEC\.\s*:?\s*([A-Z\s]+?)(?=\s*\n)'
    ]
    for pattern in kec_patterns:
        kec_match = re.search(pattern, text_upper)
        if kec_match:
            data['kecamatan'] = kec_match.group(1).strip()
            break
    
    # 10. Provinsi
    prov_match = re.search(r'PROVINSI\s+([A-Z\s]+?)(?=\s*\n)', text_upper)
    if prov_match:
        data['provinsi'] = prov_match.group(1).strip()
    
    # 11. Kabupaten/Kota
    kab_patterns = [
        r'(KABUPATEN|KOTA)\s+([A-Z\s]+?)(?=\s*\n)',
        r'WN[IA]\s+(KABUPATEN|KOTA)\s+([A-Z\s]+?)(?=\s*\n|\s*MASA|\s*\d{2}-)',
        r'(KAB\.|KOTA)\s+([A-Z\s]+?)(?=\s*\n)'
    ]
    for pattern in kab_patterns:
        kab_match = re.search(pattern, text_upper)
        if kab_match:
            prefix = kab_match.group(1).replace('KAB.', 'KABUPATEN')
            data['kabupaten_kota'] = f"{prefix} {kab_match.group(2)}".strip()
            break
    
    # 12. Agama
    agama_patterns = [
        r'AGAMA\s*:?\s*(ISLAM|KRISTEN|KATOLIK|HINDU|BUDDHA|KONGHUCU)',
        r'AGAMA\s*[-:]\s*(ISLAM|KRISTEN|KATOLIK|HINDU|BUDDHA|KONGHUCU)',
        r'AGAMA\s+(ISLAM|KRISTEN|KATOLIK|HINDU|BUDDHA|KONGHUCU)'
    ]
    for pattern in agama_patterns:
        agama_match = re.search(pattern, text_upper)
        if agama_match:
            data['agama'] = agama_match.group(1).strip()
            break
    
    # 13. Status Perkawinan
    status_patterns = [
        r'STATUS\s*PERKAWINAN\s*:?\s*(BELUM\s*KAWIN|KAWIN|CERAI\s*HIDUP|CERAI\s*MATI)',
        r'KAWIN\s*:?\s*(BELUM\s*KAWIN|KAWIN|CERAI\s*HIDUP|CERAI\s*MATI)'
    ]
    for pattern in status_patterns:
        status_match = re.search(pattern, text_upper)
        if status_match:
            status_val = status_match.group(1).strip().replace('  ', ' ')
            data['status_perkawinan'] = status_val
            data['status'] = status_val
            break
    
    # 14. Pekerjaan
    pekerjaan_match = re.search(r'PEKERJAAN\s*:?\s*([A-Z\s/]+?)(?=\s*\n|\s*KEWARGANEGARAAN)', text_upper)
    if pekerjaan_match:
        pekerjaan_raw = pekerjaan_match.group(1).strip()
        pekerjaan_clean = re.split(r'\s+(WNI|WNA|KOTA|KABUPATEN)\s+', pekerjaan_raw)[0].strip()
        data['pekerjaan'] = pekerjaan_clean
    
    # 15. Kewarganegaraan
    warga_match = re.search(r'KEWARGANEGARAAN\s*:?\s*(WNI|WNA)', text_upper)
    if warga_match:
        data['kewarganegaraan'] = warga_match.group(1).strip()
    
    return data

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'OK',
        'service': 'OCR KTP Extractor',
        'version': '1.0.0',
        'timestamp': datetime.now().isoformat()
    })

@app.route('/test-api', methods=['GET'])
def test_api():
    """Test koneksi ke OCR.Space"""
    log_info("Testing OCR.Space API connection...")
    
    try:
        # Test dengan URL sederhana
        response = requests.get('https://api.ocr.space/', timeout=10)
        
        return jsonify({
            'success': True,
            'message': 'OCR.Space API reachable',
            'status_code': response.status_code,
            'api_key': API_KEY[:10] + '...'
        })
    except Exception as e:
        return jsonify({
            'success': False,
            'message': str(e),
            'api_key': API_KEY[:10] + '...'
        }), 500

if __name__ == '__main__':
    print("="*60)
    print("🚀 OCR SERVER - PRODUCTION READY")
    print("="*60)
    print(f"API Key: {API_KEY[:10]}... (from env)")
    print(f"Debug Mode: {DEBUG_MODE}")
    print("Endpoints:")
    print("  - POST http://127.0.0.1:8001/extract-ktp")
    print("  - GET  http://127.0.0.1:8001/health")
    print("  - GET  http://127.0.0.1:8001/test-api")
    print("="*60)
    print("\n⚠️  PENTING:")
    print("Jika error 404 terus terjadi:")
    print("1. Pastikan API key valid")
    print("2. Daftar API key gratis di: https://ocr.space/ocrapi")
    print("3. Copy API key ke file .env")
    print("4. Restart server")
    print("="*60)
    app.run(host='127.0.0.1', port=8001, debug=True)