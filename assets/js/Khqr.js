document.addEventListener("DOMContentLoaded", function () {
    const KHQR = (typeof BakongKHQR !== "undefined") ? BakongKHQR : null;
  
    if (KHQR) {
      const data = KHQR.khqrData;
      const info = KHQR.IndividualInfo;
  
      const optionalData = {
        currency: data.currency.usd,
        amount: 0.01,
        mobileNumber: "85515309275",
        storeLabel: "Coffee Shop",
        terminalLabel: "Cashier 1",
        purposeOfTransaction: "Oversea",
        languagePreference: "km",
        merchantNameAlternateLanguage: "ចន ស្មី",
        merchantCityAlternateLanguage: "ភ្នំពេញ",
        upiMerchantAccount: "000010344000010344ABCDEFGHIJKLMN0",
      };
  
      const individualInfo = new info("kana_ty@acib", "Kana Ty", "Phnom Penh", optionalData);
      const khqrInstance = new KHQR.BakongKHQR();
      const individual = khqrInstance.generateIndividual(individualInfo);

      // Store the QR code data for later use
      let qrCodeData = individual ? individual.data.qr : null;
      let md5Value = individual ? individual.data.md5 : null;

      const displayQRCode = () => {
        if (qrCodeData) {
            const qrCodeCanvas = document.getElementById("qrCodeCanvas");
            QRCode.toCanvas(qrCodeCanvas, qrCodeData, function (error) {
                if (error) console.error(error);
            });
            qrCodeCanvas.style.width = '100%';
            qrCodeCanvas.style.height = '100%';
            const qrCodeModal = new bootstrap.Modal(document.getElementById("qrCodeModal"));
            qrCodeModal.show();
        } else {
            console.error("QR code data is not available.");
        }
      };

      const checkoutButton = document.getElementById("checkout");
      if (checkoutButton) {
          checkoutButton.addEventListener("click", displayQRCode);
      } 

      let checkTransactionInterval;

      function startQRCodeScanner() {
          if (md5Value) {
              checkTransactionInterval = setInterval(() => {
                  fetchTransactionStatus(md5Value);
              }, 5000);
          } else {
              console.error("MD5 value is not available.");
          }
      }

      $('#qrCodeModal').on('show.bs.modal', function (e) {
          startQRCodeScanner();
      });

      function fetchTransactionStatus(md5) {
          const token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7ImlkIjoiNzRhM2ZjOGRhYzRlNDBiYSJ9LCJpYXQiOjE3NDUzMzAwMjgsImV4cCI6MTc1MzEwNjAyOH0.Tpi2_a2t9nKy9rHbgwRKKXhR-JS9FfpU5uLaNSt8XRg";
          const url = "https://api-bakong.nbc.gov.kh/v1/check_transaction_by_md5";
        
          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
              md5: md5
            })
          })
          .then(response => response.json())
          .then(data => {
              if (data.responseMessage === 'Success') {
                  window.location.href = 'index.php?msg=order+successful';
                  clearInterval(checkTransactionInterval);
              }
          })
          .catch(error => {
            console.error('Error checking transaction:', error);
            clearInterval(checkTransactionInterval);
          });
      }
    } else {
        console.error("BakongKHQR or its components are not loaded or defined.");
    }
});