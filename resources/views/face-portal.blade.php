<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Face DTR</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            background: #000;
            font-family: 'Figtree', sans-serif;
            overflow: hidden;
        }
        
        #container {
            position: relative;
            width: 100vw;
            height: 100vh;
            background: #000;
            overflow: hidden;
        }


        video, canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
        }

        #guide {
            position:absolute;
            top:50%;
            left:50%;
            width: min(40vw, 160px);
            height: min(40vw, 160px);
            max-width: 180px;
            max-height: 180px;
            transform:translate(-50%,-50%);
            border:3px dashed rgba(0,255,0,.6);
            border-radius:12px;
            z-index:10;
            pointer-events:none;
        }

        #result {
            position:absolute;
            bottom:90px;
            width:100%;
            text-align:center;
            font-size:16px;
            font-weight:600;
            color:#fff;
            z-index:15;
        }
        
        #marks {
            position: absolute;
            top: 50px;
            left: 50%;
            transform: translateX(-50%);
            width: 95%;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            z-index: 20;
        }
        
        #marks > div {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 6px;
            padding: 6px 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        
        #marks input[type="radio"] {
            accent-color: #00ff88;
        }
        
        #marks label {
            font-size: 11px;
            color: #fff;
            cursor: pointer;
        }

    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('face-portal/face-api.min.js') }}"></script>
</head>
<body>

<div id="container">
    <video id="video" autoplay muted></video>
    <div id="guide"></div>
    <div id="result"></div>
    <div id="marks">
        <div>
            <input type="radio" id="m1" name="mark" class="mark" value="am_in" checked />
            <label for="m1">AM In</label>
        </div>
        <div>
            <input type="radio" id="m2" name="mark" class="mark" value="am_out" />
            <label for="m2">AM Out</label>
        </div>
        <div>
            <input type="radio" id="m3" name="mark" class="mark" value="pm_in" />
            <label for="m3">PM In</label>
        </div>
        <div>
            <input type="radio" id="m4" name="mark" class="mark" value="pm_out" />
            <label for="m4">PM Out</label>
        </div>
        <div>
            <input type="radio" id="m5" name="mark" class="mark" value="ot_in" />
            <label for="m5">OT In</label>
        </div>
        <div>
            <input type="radio" id="m6" name="mark" class="mark" value="ot_out" />
            <label for="m6">OT Out</label>
        </div>
    </div>
</div>

<script>
    const employees = @json($employees);
    const video = document.getElementById('video');
    const resultBox = document.getElementById('result');

    let lastDetectionTime = 0;
    let imgPause = 0;
    let submitting = false;
    let lastRecognizedId = null;

    async function startVideo() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video:true });
            video.srcObject = stream;
        } catch (e) {
            alert('Camera access denied');
        }
    }

    Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri("{{ asset('face-portal/models') }}"),
        faceapi.nets.faceLandmark68Net.loadFromUri("{{ asset('face-portal/models') }}"),
        faceapi.nets.faceRecognitionNet.loadFromUri("{{ asset('face-portal/models') }}"),
    ]).then(startVideo);

    async function loadLabeledImages() {
        return Promise.all(
            employees.map(async emp => {

                const images = [
                    emp.photo_lg,
                    emp.photo_lg2,
                    emp.photo_lg3
                ].filter(Boolean);

                const descriptors = [];

                for (const photo of images) {
                    try {
                        const img = await faceapi.fetchImage(photo);
                        const detection = await faceapi
                            .detectSingleFace(img)
                            .withFaceLandmarks()
                            .withFaceDescriptor();

                        if (detection) descriptors.push(detection.descriptor);
                    } catch {
                        console.warn('Image skipped:', photo);
                    }
                }

                return new faceapi.LabeledFaceDescriptors(
                    String(emp.id),
                    descriptors
                );
            })
        );
    }
    
    function formatDateTime(date = new Date()) {
          const pad = n => String(n).padStart(2, "0");
          return (
            date.getFullYear() + "-" +
            pad(date.getMonth() + 1) + "-" +
            pad(date.getDate()) + " " +
            pad(date.getHours()) + ":" +
            pad(date.getMinutes()) + ":00"
          );
    }
    
    function formatDate(date = new Date()) {
          const pad = n => String(n).padStart(2, "0");
          return (
            date.getFullYear() + "-" +
            pad(date.getMonth() + 1) + "-" +
            pad(date.getDate())
          );
    }

    video.addEventListener('play', async () => {
        const labeledDescriptors = await loadLabeledImages();
        const matcher = new faceapi.FaceMatcher(labeledDescriptors, 0.5);
        const displaySize = { width:360, height:740 };

        async function detect() {
            const now = Date.now();
            if (now - lastDetectionTime < 250) {
                requestAnimationFrame(detect);
                return;
            }
            lastDetectionTime = now;

            const detections = await faceapi
                .detectAllFaces(video)
                .withFaceLandmarks()
                .withFaceDescriptors();

            const resized = faceapi.resizeResults(detections, displaySize);

            resized.forEach((detection) => {
                const bestMatch = matcher.findBestMatch(detection.descriptor);

                if (
                    bestMatch.label !== 'unknown' &&
                    bestMatch.distance < 0.5
                ) {

                    if (lastRecognizedId === bestMatch.label) return;

                    imgPause++;
                    resultBox.innerHTML = `<div style="color:#0f0">Recognizing...</div>`;

                    if (imgPause >= 5 && !submitting) {
                        submitting = true;
                        lastRecognizedId = bestMatch.label;
                        
                        const mark = document.querySelector('input[name="mark"]:checked');

                        $.post('/api/dtr/make-log', {
                            employee_id: bestMatch.label,
                            mark: mark.value,
                            date_log: formatDate(),
                            time_log: formatDateTime
                        })
                        .done(res => {
                        
                            if (res.status === 'success') {
                                resultBox.innerHTML = `
                                    <div style="color:#00ff88;font-size:18px">
                                        <div>✔ ${res.message}</div>
                                        <div>${res.data.fullname}</div>
                                        <div style="font-size:14px">${res.data.time}</div>
                                    </div>
                                `;
                            } 
                            else if (res.status === 'duplicate') {
                                resultBox.innerHTML = `
                                    <div style="color:#ffcc00;font-size:18px">
                                        <div>⚠ ${res.message}</div>
                                        <div>${res.data.fullname}</div>
                                    </div>
                                `;
                            }
                            else {
                                resultBox.innerHTML = `
                                    <div style="color:#ff4d4d;font-size:18px">
                                        ✖ ${res.message || 'Failed'}
                                    </div>
                                `;
                            }
                        
                        })
                        .fail(xhr => {
                            resultBox.innerHTML = `
                                <div style="color:#ff4d4d;font-size:18px">
                                    ✖ ${xhr.responseJSON?.message || 'Server error'}
                                </div>
                            `;
                        })
                        .always(() => {
                            setTimeout(() => {
                                submitting = false;
                                imgPause = 0;
                                lastRecognizedId = null;
                                resultBox.innerHTML = '';
                            }, 4000);
                        });
                        

                    }
                } else {
                    imgPause = 0;
                }
            });

            requestAnimationFrame(detect);
        }

        detect();
    });
</script>

</body>
</html>
