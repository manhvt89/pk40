<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tích Hợp Google Cloud Text-to-Speech</title>
</head>
<body>
    <textarea id="text-to-read" rows="4" cols="50" placeholder="Nhập văn bản bạn muốn đọc..."></textarea><br>
    <button onclick="readText()">Đọc Văn Bản</button>

    <audio style="display:none;" id="audio-output" controls></audio>

    <script>
        async function readText() {
            const text = document.getElementById('text-to-read').value;

            const response = await fetch('<?= base_url('tts/synthesize') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({ text: text })
            });

            if (response.ok) {
                const audioBlob = await response.blob();
                const audioUrl = URL.createObjectURL(audioBlob);
                const audioElement = document.getElementById('audio-output');
                audioElement.src = audioUrl;
                audioElement.play();
            } else {
                console.error('Có lỗi xảy ra khi gọi API Text-to-Speech');
            }
        }
    </script>
</body>
</html>
