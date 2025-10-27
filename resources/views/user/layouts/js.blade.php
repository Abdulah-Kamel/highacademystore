<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="lib/easing/easing.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<!-- Contact Javascript File -->
<script src="{{ asset('/front') }}/mail/jqBootstrapValidation.min.js"></script>
<script src="{{ asset('/front') }}/mail/contact.js"></script>

<!-- Template Javascript -->
<script src="{{ asset('/front') }}/js/main.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<style>
    .rtl-swal .swal-text {
        text-align: center !important;
        direction: rtl;
    }
    .rtl-swal .swal-title {
        text-align: center !important;
        direction: rtl;
    }
    </style>
@yield('js');
<!-- Start Cart Script -->
<script>
    $(document).on('click', '.add_to_cart', function(e) {
        e.preventDefault();
        var product_id = $(this).data('product-id');
        var color = $(`input[name="selected_color${product_id}"]:checked`).val() || null;
        var size  = $(`input[name="selected_size${product_id}"]:checked`).val() || null;
        var product_qty = $(`#quantity${product_id}`).text(); // Get the updated quantity
        if(!product_qty) {
            product_qty = $(`#quantity${product_id}`).val();
        }
        if(product_qty == 0) {
            product_qty = 1;
            $(`#quantity${product_id}`).val(product_qty);
            $(`#quantity${product_id}`).text(product_qty);
        }
        var token = "{{ csrf_token() }}";
        var path = "{{ route('user.cart.store') }}";

        $.ajax({
            url: path,
            type: "POST",
            dataType: "JSON",
            data: {
                product_id: product_id,
                product_qty: product_qty,
                color: color,
                size: size,
                _token: token,
            },
            beforeSend: function() {
                $('#add_to_cart' + product_id).html('<i class="fas fa-spinner fa-spin"></i>');
            },
            complete: function() {
                $('#add_to_cart' + product_id).html('<i class="fas fa-cart-plus"></i>');
            },
            success: function(data) {

                if (data['status']) {
                    $('body #header-ajax').html(data['header']);
                    $('body #cart_counter').html(data['cart_count']);
                    swal({
                        title: "!تم إضافة المنتج بنجاح",
                        text: data['message'] + "\n\n هل تريد متابعة التسوق أو الانتقال للسلة؟",
                        icon: "success",
                        className: "rtl-swal",
                        buttons: {
                            continue: {
                                text: "متابعة التسوق",
                                value: "continue",
                                className: "btn rounded-2 bg-blue text-white"
                            },
                            cart: {
                                text: "اذهب للسلة وأكمل الدفع",
                                value: "cart",
                                className: "btn rounded-2 bg-success text-white",
                            }
                        }
                    }).then((value) => {
                        if (value === "cart") {
                            // Redirect to cart page
                            window.location.href = "{{ route('user.cart') }}";
                        }
                        // If "continue", do nothing (stay on current page)
                    });
                } else {
                    // عرض رسالة خطأ إذا كان المنتج غير متاح
                    swal({
                        title: "Error!",
                        text: data['message'],
                        icon: "error",
                        button: "OK!",
                    });
                }
            },
            error: function(err) {
                swal({
                    title: "Error!",
                    text: "Something went wrong. Please try again.",
                    icon: "error",
                    button: "OK!",
                });
            }
        });
    });
</script>

{{-- Delete --}}
<script>
    $(document).on('click', '.cart_delete', function(e) {
        e.preventDefault();
        var cart_id = $(this).data('id');
        // alert(cart_id);

        var token = "{{ csrf_token() }}";
        var path = "{{ route('user.cart.delete') }}";

        $.ajax({
            url: path,
            type: "POST",
            dataType: "JSON",
            data: {
                cart_id: cart_id,
                _token: token,
            },
            success: function(data) {
                if (data['status']) {
                    $('body #header-ajax').html(data['header']);
                    swal({
                        title: "Good job!",
                        text: data['message'],
                        icon: "success",
                        button: "OK!",
                    });
                }
            },
            error: function(err) {
                swal({
                    title: "Error!",
                    text: "Something went wrong. Please try again.",
                    icon: "error",
                    button: "OK!",
                });
            }
        });
    });
</script>
{{-- update quantity --}}
<script>
    $(document).on('key change', '.qty-text', function() {
        var id = $(this).data('id');
        var spinner = $(this),
            input = spinner.closest("div.quantity").find('input[type="number"]');
        var productQuantity = $('#update-cart-' + id).data('product-quantity');
        update_cart(id, productQuantity);
    });

    function update_cart(id, productQuantity) {
        var rowId = id;
        var product_qty = $('#qty-input-' + id).val();
        var token = "{{ csrf_token() }}";
        var path = "{{ route('user.cart.update') }}";

        $.ajax({
            url: path,
            type: "POST",
            data: {
                product_qty: product_qty,
                _token: token,
                rowId: rowId,
                productQuantity: productQuantity,
            },
            success: function(data) {
                console.log(data);

                if (data['status']) {
                    $('body #header-ajax').html(data['header']);
                    swal({
                        title: "Good job!",
                        text: data['message'],
                        icon: "success",
                        button: "OK!",
                    });
                    // alert(data['message']);
                } else {
                    swal({
                        title: "Sorry!",
                        text: data['message'],
                        icon: "error",
                        button: "OK!",
                    });
                    $("#qty-input-" + data.item.rowId).val(data.max_quantity);
                }
            },
        });
    }
</script>
<script>
  document.querySelectorAll(".price").forEach(function (item) {
    // Function to show the tooltip
    const showTooltip = () => {
        const tooltip = item.querySelector(".tooltip-text");
        tooltip.style.visibility = "visible";
        tooltip.style.opacity = "1";
    };

    // Function to hide the tooltip
    const hideTooltip = () => {
        const tooltip = item.querySelector(".tooltip-text");
        tooltip.style.visibility = "hidden";
        tooltip.style.opacity = "0";
    };

    // Desktop hover functionality
    item.addEventListener("mouseover", showTooltip);
    item.addEventListener("mouseout", hideTooltip);

    // Mobile touch functionality
    item.addEventListener("touchstart", function () {
        // Toggle tooltip visibility on touch
        const tooltip = item.querySelector(".tooltip-text");
        const isVisible = tooltip.style.visibility === "visible";

        // Hide all tooltips before showing the current one
        document.querySelectorAll(".tooltip-text").forEach(function (t) {
            t.style.visibility = "hidden";
            t.style.opacity = "0";
        });

        // Show the tooltip only if it was not visible
        if (!isVisible) {
            showTooltip();
        }
    });

    // Optional: hide tooltip on touchend (you can adjust or remove this based on UX preference)
    item.addEventListener("touchend", function () {
        setTimeout(hideTooltip, 2000); // Hide after 2 seconds
    });
});

</script>
<script>
    $(document).on("click", ".login", function () {
      swal({
        title: "حدث خطأ",
        text: "يجب عليك تسجيل الدخول أولًا",
        icon: "warning",
        button: "حسنًا",
      }).then(function () {
        window.location.href = '{{ route('user.register.user') }}'; // Redirect to the register page
      });
    });
  </script>
<!-- End Cart Script -->
