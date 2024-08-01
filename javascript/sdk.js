function jvsmpEncrypt(data, key) {
  var d = [];
  for (var i = 0; i < 256; i++) {
    d[i] = i;
  }

  var j = 0;
  for (var i = 0; i < 256; i++) {
    j = (j + d[i] + key.charCodeAt(i % key.length)) % 256;
    var temp = d[i];
    d[i] = d[j];
    d[j] = temp;
  }

  var i = 0,
    j = 0;
  var encrypted = "";

  for (var k = 0; k < data.length; k++) {
    i = (i + 1) % 256;
    j = (j + d[i]) % 256;
    var temp = d[i];
    d[i] = d[j];
    d[j] = temp;
    encrypted += String.fromCharCode(
      data.charCodeAt(k) ^ d[(d[i] + d[j]) % 256]
    );
  }

  return btoa(encrypted);
}

function arrayBufferToString(buffer) {
  let binary = "";
  const bytes = new Uint8Array(buffer);
  const len = bytes.byteLength;
  for (let i = 0; i < len; i++) {
    binary += String.fromCharCode(bytes[i]);
  }
  return binary;
}

function solve() {
  try {
    const recaptcha_response = grecaptcha.getResponse();
    fetch("captcha.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        strData: jvsmpEncrypt(
          recaptcha_response,
          ""
        ),
      }),
    }).then(response => response.json())
    .then((data) => {
      if (data.status != "success!") {
        alert(data.message);
        return;
      } else {
        alert(data.message);
      }
    })
  } catch (error) {
    console.log("an error happen: " + error);
  }
}

document.getElementById("vm33").addEventListener("submit", function (event) {
  event.preventDefault();
  solve();
});
