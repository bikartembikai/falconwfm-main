from pypdf import PdfReader
import sys
import io

# Force stdout to use utf-8
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

try:
    reader = PdfReader("FYP Report (10).pdf")
    print("Searching for Chapter 2.6...")
    for i, page in enumerate(reader.pages):
        content = page.extract_text()
        if "System Architecture" in content or "2.6" in content:
            print(f"--- Page {i+1} ---")
            print(content)
            print("----------------")
except Exception as e:
    print(f"Error reading PDF: {e}")
