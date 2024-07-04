<!DOCTYPE html>
<html>
<head>
  <title>Barcode Scanner</title>
</head>
<body>
  <h1>Barcode Scanner</h1>
  <div id="interactive" style="width: 640px; height: 480px;"></div>
  <script src="quagga.min.js"></script>
</body>
</html>
<script>
  // Configuration for QuaggaJS
  const config = {
    inputStream: {
      name: "Live",
      type: "LiveStream",
      target: "#interactive",
    },
    decoder: {
      readers: ["code_128_reader"],
    },
  };

  // Initialize QuaggaJS with the provided configuration
  Quagga.init(config, function (err) {
    if (err) {
      console.error("Error initializing Quagga:", err);
      return;
    }

    // Once QuaggaJS is initialized, start the scanner
    Quagga.start();

    // Add event listener to handle scanned results
    Quagga.onDetected(function (result) {
      const code = result.codeResult.code;
      // Show an alert with the scanned barcode
      alert("Scanned Barcode: " + code);
      Quagga.stop();

    });
  });
</script>