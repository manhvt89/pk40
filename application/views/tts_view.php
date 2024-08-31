<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tích Hợp AI cho tự động hóa gọi Tên theo thứ tự ưu tiên - ManhVT - 0936111917 </title>
   
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.18.3/dist/bootstrap-table.min.css">

</head>
<body>
    <div><h1>Danh sánh bệnh nhân chờ khám</h1></div>
<table
        id="table"
        data-toggle="table"
        data-height="460"
        data-url="/tts/list_waiting"
        data-search="true"
        data-side-pagination="server"
        data-pagination="true">
        <thead>
            <tr>
                <th data-field="id">ID</th>
                <th data-field="name">Tên bệnh nhân</th>
                <th data-field="read" data-events="playEvents">...</th>
                <th data-field="dob">Ngày sinh</th>
                <th data-field="add">Địa chỉ</th>
            </tr>
        </thead>
    </table>

    



    
       
    <audio id="audio-output" style="display: none;"></audio>
    

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>
    <script>
        // your custom ajax request here
        window.playEvents = {
            'click .play_audio_btn': function (e, value, row, index) {
                playAudio(e, null, row, index);
            },
            
         }

         async function playAudio(event, value, row, index) {
   
            console.log(row);
            console.log(index);
            if(row.call_name == '')
            {
            } else {
            text = row.call_name;
            }
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
                console.error('Có lỗi xảy ra khi gọi API');
            }
            } 
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
