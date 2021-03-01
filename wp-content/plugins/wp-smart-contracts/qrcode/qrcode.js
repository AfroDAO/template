var QRCode = require('qrcode')

QRCode.toDataURL('0xc3164C6E32685A223CD7B7b56b15df19A4B87ea9', function (err, url) {
  console.log(url)
});

