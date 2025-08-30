@extends('user.layouts.master')

@section('title')
    كوبوناتي
@endsection

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <style>
        .hidden {
            display: none;
        }

        .unselectable {
            user-select: none;
            pointer-events: none;
        }

        .selectable {
            user-select: auto;
            pointer-events: auto;
        }

        .bg-warning,
        .btn-primary {
            background-color: #e99239 !important;
        }

        .text-primary,
        text-warning {
            color: #e99239 !important;
        }

        .btn-primary {
            border: none;
        }

        .scratch-area {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    @media screen and (max-width: 600px) {
       .scratch-area {
           height: 150px;
       }
    }
        .scratch-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            cursor: grab;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            display: flex;
            justify-content: center;
            padding-top: 20px;
            font-size: 1.5rem;
            z-index: 20;
        }

        /* Bounce Animation */
        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-30px);
            }

            60% {
                transform: translateY(-15px);
            }
        }

        .bounce {
            animation: bounce 1s ease;
        }

        .voucher-code {
            text-transform: none !important;
        }
    </style>

    <div class="container pt-5">
        <div class="row text-center pt-5 mt-5">
            <div class="col-md-12">
                <h5 class="section-title position-relative text-uppercase mb-3">
                    <span class="pr-3">كوبونــــــــــاتي</span>
                </h5>
            </div>

            <div class="row mx-auto" style="width: 100%;">
                @if (count($vouchers) == 0)
                    <h3 class="mt-5">لا يوجد اي كوبونات لعرضها</h3>
                @endif

                @foreach ($vouchers as $voucher)
                    <div class="col-md-6">
                        <div class="card mt-3 mx-auto" style="width: 100%;" dir="rtl">
                            <div class="card-header text-center">
                                <strong class="title justify-content-center">
                                    {{ $voucher->coupon->name }}
                                </strong>
                            </div>

                            <div class="card-body p-0">
                                <div class="scratch-area" id="scratch-area-{{ $voucher->id }}">
                                    <center>
                                        <h1 class="voucher-code unselectable fw-bold" id="code-{{ $voucher->id }}">
                                           {{ $voucher->code }}
                                        </h1>
                                    </center>
                                    <canvas class="scratch-canvas" id="canvas-{{ $voucher->id }}"></canvas>
                                    <div class="overlay" id="overlay-{{ $voucher->id }}">
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-muted">
                                <center>
                                    <h6>{{ $voucher->updated_at }}</h6>
                                </center>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Canvas Confetti Script -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const vouchers = @json($vouchers);

            const fireConfetti = () => {
                confetti({
                    particleCount: 150,
                    spread: 70,
                    ticks: 70,
                    origin: {
                        y: 0.6,
                    }
                });
            };

            vouchers.forEach(voucher => {
                const canvas = document.getElementById(`canvas-${voucher.id}`);
                const overlay = document.getElementById(`overlay-${voucher.id}`);
                const codeElement = document.getElementById(`code-${voucher.id}`);
                const context = canvas.getContext("2d");

                const scratchArea = canvas.parentElement;
                canvas.width = scratchArea.clientWidth;
                canvas.height = scratchArea.clientHeight;

                const img = new Image();
                img.crossOrigin = "anonymous";
                img.src = voucher.coupon.image ? `/${voucher.coupon.image}` : "";

                img.onload = () => {
                    context.drawImage(img, 0, 0, canvas.width, canvas.height);
                };

                img.onerror = () => {
                    context.fillStyle = "#add8e6";
                    context.fillRect(0, 0, canvas.width, canvas.height);
                };

                const voucherUpdatedAt = new Date(voucher.updated_at);
                const currentTime = new Date();
                const hoursSinceUpdate = Math.abs(currentTime - voucherUpdatedAt) / 36e5;

                if (hoursSinceUpdate > 24) {
                    overlay.style.display = "flex";
                    canvas.style.display = "none";
                } else {
                    overlay.style.display = "none";
                    canvas.style.display = "block";
                    canvas.style.pointerEvents = "auto";
                }

                let isDragging = false;

                const scratch = (x, y) => {
                    context.globalCompositeOperation = "destination-out";
                    context.beginPath();
                    context.arc(x, y, 25, 0, 2 * Math.PI);
                    context.fill();
                };

                const calculateRemovedPercentage = () => {
                    let imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    let pixels = imageData.data;
                    let totalPixels = pixels.length / 4;
                    let transparentPixels = 0;

                    for (let i = 0; i < pixels.length; i += 4) {
                        if (pixels[i + 3] === 0) {
                            transparentPixels++;
                        }
                    }
                    return (transparentPixels / totalPixels) * 100;
                };

                const startScratch = (x, y) => {
                    isDragging = true;
                    scratch(x, y);
                };

                const continueScratch = (x, y) => {
                    if (isDragging) {
                        scratch(x, y);
                        if (calculateRemovedPercentage() > 70) {
                            canvas.style.display = "none";
                            const card = canvas.closest(".card");
                            card.classList.add("bounce");

                            fireConfetti(canvas);

                            setTimeout(() => {
                                card.classList.remove("bounce");
                            }, 1000);
                              codeElement.classList.remove("unselectable");
                    codeElement.classList.add("selectable");
                        }
                    }
                };

                const stopScratch = () => {
                    isDragging = false;
                };

                canvas.addEventListener("mousedown", (e) => {
                    startScratch(e.offsetX, e.offsetY);
                });

                canvas.addEventListener("mousemove", (e) => {
                    continueScratch(e.offsetX, e.offsetY);
                });

                canvas.addEventListener("mouseup", stopScratch);
                canvas.addEventListener("mouseleave", stopScratch);

                canvas.addEventListener("touchstart", (e) => {
                    const rect = canvas.getBoundingClientRect();
                    const touch = e.touches[0];
                    const x = touch.clientX - rect.left;
                    const y = touch.clientY - rect.top;
                    startScratch(x, y);
                });

                canvas.addEventListener("touchmove", (e) => {
                    e.preventDefault();
                    const rect = canvas.getBoundingClientRect();
                    const touch = e.touches[0];
                    const x = touch.clientX - rect.left;
                    const y = touch.clientY - rect.top;
                    continueScratch(x, y);
                }, {
                    passive: false
                });

                canvas.addEventListener("touchend", stopScratch);

            });
        });
    </script>
@endsection
