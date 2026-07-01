
$(document).ready(function () {
    showcart();
    $(document).on('click', '#listToppingCheck .qty-box', function(e) {
        const qtyBox = e.target.closest('.qty-box');
        if (!qtyBox) return; // Không click vào .qty-box thì bỏ qua

        const target = e.target;

        const input = qtyBox.querySelector('.qty-input');
        let value = parseInt(input.value) || 1;
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || Infinity;

        if (target.classList.contains('quantity-left-minus')) {
            value = Math.max(min, value - 1);
            input.value = value;
        }
        if (target.classList.contains('quantity-right-plus')) {
            value = Math.min(max, value + 1);
            input.value = value;
        }
    });
    $(document).on('click', '#qtyProduct .qty-box', function(e) {
        const qtyBox = e.target.closest('.qty-box');
        if (!qtyBox) return; // Không click vào .qty-box thì bỏ qua
        console.log(qtyBox);
        const target = e.target;

        const input = qtyBox.querySelector('.qty-adj');
        let value = parseInt(input.value) || 1;
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max) || Infinity;
        // tại sao 
        if (target.classList.contains('qty-minus')) {
            value = Math.max(min, value - 1);
            input.value = value;
        }
        if (target.classList.contains('qty-plus')) {
            value = Math.min(max, value + 1);
            input.value = value;
        }
    });
});

function login() {
    let name = $('#loginUser').val();
    let pass = $('#loginPass').val();
    if (name == "" && pass == '') {
        notify('Vui lòng nhập tài khoản.', 'danger', true);
        $('#loginUser').focus();
        return false;
    } else if (pass == "" && name != "") {
        notify('Vui lòng nhập mật khẩu.', 'danger', true);
        $('#loginPass').focus();
        return false;
    } else if (pass != "" && name == "") {
        notify('Vui lòng nhập tài khoản.', 'danger', true);
        $('#loginUser').focus();
        return false;
    } else if (name != '' && pass != '') {
        var url = root + 'login';
        $.post(url, {
            user: name,
            pass: pass,
            csrf_token: $('#csrf_token').val()
        }, function (res) {
            $('#csrf_token').val(res.key);
            if (res.status) {
                notify('Đăng nhập thành công.', 'primary', true);
                window.location.href = root
            } else {
                notify('Thông tin đăng nhập không đúng, vui lòng kiểm tra.', 'danger', true);
            }

        });
    }
}

function notify(ms, type, status) {
    //primary, secondary, success, warning, info, danger
    if (status) {
        $.notify({
            icon: 'fa fa-check',
            title: 'Vui lòng đợi!',
            message: ms
        }, {
            element: 'body',
            position: null,
            type: type,
            allow_dismiss: true,
            newest_on_top: false,
            showProgressbar: true,
            placement: {
                from: "top",
                align: "right"
            },
            offset: 20,
            spacing: 10,
            z_index: 1031,
            delay: 2000,
            animate: {
                enter: 'animated bounceInDown',
                exit: 'animated bounceOutUp'
            },
            icon_type: 'class',
            template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
                '<button type="button" aria-hidden="true" class="btn-close notify-close" data-notify="dismiss"></button>' +
                '<strong><span data-notify="icon"></span> ' +
                '<span data-notify="message">{2}</span></strong>' +
                '<div class="progress  mt-2" data-notify="progressbar">' +
                '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                '</div>' +
                '<a href="{3}" target="{4}" data-notify="url"></a>' +
                '</div>'
        });
    } else {
        $.notify({
            message: ms
        }, {
            type: type,
            delay: 2000,
        });
    }
}

function addCart(id) {
    var toppingSelected = [];
    for (var i = 0; i < $("form#listToppingCheck :checkbox").length; i++) {
        if (document.getElementById('topping' + i) != null) {
            itemCheck = document.getElementById('topping' + i);
            if (itemCheck.checked) {
                let $checkbox = $('#topping' + i);
                let $toppingContainer = $checkbox.closest('.collection-filter-checkbox');
                let $qtyBox = $toppingContainer.find('.qty-box');
                let qtyValue = 1
                if ($qtyBox.length) {
                    qtyValue = Number($qtyBox.find('.qty-input').val());
                }
                let item = {
                    id: $('#topping' + i).val(),
                    qty: qtyValue
                };
                toppingSelected.push(item);
            }
        }
    }
    let size = $('input[name="sizeProduct"]:checked').val();
    let amount = $('#qtyItem' + id).val();
    let note = $('#note').val();
    // Checkbox được render theo id sản phẩm: isCupCustomer{id}
    let $cupCustomerCheckbox = $('#isCupCustomer' + id);
    if (!$cupCustomerCheckbox.length) {
        $cupCustomerCheckbox = $('#isCupCustomer');
    }
    let isCupCustomer = $cupCustomerCheckbox.length && $cupCustomerCheckbox.prop('checked') ? 1 : 0;
    if (!toppingSelected.length) {
        toppingSelected = toppingSelected.toString()
    }
    $.post(root + 'home/addcart', {
        id: id,
        size: size,
        amount: amount,
        note: note,
        isCupCustomer: isCupCustomer,
        topping: toppingSelected,
        csrf_token: $('#csrf_token').val()
    }, function (res) {
        // var res = JSON.parse(data);
        $('#csrf_token').val(res.key);
        if (res.status) {
            notify('Đã thêm sản phẩm thành công.', 'primary', true);
            $('#exampleModal').modal('hide');
            $('#count-cart-product').html(parseInt(res.countCart));
            // nếu đang ở trang xác nhận đơn hàng thì reload để cập nhật lại giỏ hàng
            let curHref = window.location.href
            if (curHref.includes("xac-nhan-don-hang")) {
                location.reload()
            }
        } else {
            notify('Sản phẩm không thể thêm vào giỏ hàng.', 'danger', true);
        }
    });
}

function showcart() {
    $('#cart_side').load(root + 'home/viewQuickCart', {
        csrf_token: $("#csrf_token").val()
    })
}

function removecart(id, index) {
    closeCart();
    $.post(root + 'home/removeCart', {
        id: id,
        index: index,
        csrf_token: $('#csrf_token').val()
    }, function (res) {
        closeCart();
        // var res = JSON.parse(data)
        $('#csrf_token').val(res.key);
        if (res.status) {
            notify('Đã xóa sản phẩm khỏi đơn hàng.', 'success', true);
            $('#count-cart-product').html(parseInt(res.countCart));
            let curHref = window.location.href
            if (curHref.includes("xac-nhan-don-hang")) {
                location.reload()
            }
            openCart()
        } else {
            notify('Không thể xóa sản phẩm.', 'danger', true);
        }
    });
}

function updateItemCart(id, index) {
    let qty = $('#qtyItem' + index).val()
    closeCart();
    $.post(root + 'home/updateItemCart', {
        id: id,
        index: index,
        qty: qty,
        csrf_token: $('#csrf_token').val()
    }, function (res) {
        // var res = JSON.parse(data)
        $('#csrf_token').val(res.key);
        if (res.status) {
            notify('Đã thêm số lượng sản phẩm.', 'success', true);
            $('#count-cart-product').html(parseInt(res.countCart));
            let curHref = window.location.href
            if (curHref.includes("xac-nhan-don-hang")) {
                location.reload()
            }
            openCart()
        } else {
            notify('Không thể thêm số lượng sản phẩm.', 'danger', true);
        }
    });
}

function checkout() {
    $('.loader-wrapper').addClass('active');
    $('#btnCheckout').prop('disabled', true);
    let delivery = $('input[name="orderType"]:checked').val();
    let payment = $('input[name="orderPayment"]:checked').val();
    let note = $('#note').val()
    let rawValue = $('#shippingPrice').text();
    let shippingFee = Number(rawValue.replace(/\D/g, ''));
    let discount = $('#discountPrice').text() ? Number($('#discountPrice').text().replace(/\D/g, '')) : 0;
    let discountCode = $('#couponCode').val();
    let discountName = $('#couponCode').val() ? $('#discountName').text() : '';
    $.post(root + 'home/checkoutCart', {
        delivery: delivery,
        payment: payment,
        note: note,
        shippingFee: shippingFee || 0,
        discount: discount || 0,
        discountCode: discountCode || '',
        discountName: discountName || '',
        csrf_token: $('#csrf_token').val()
    }, function (res) {
        // var res = JSON.parse(data);
        $('#csrf_token').val(res.key);
        if (res.status) {
            notify('Đơn hàng đã được đặt hàng thành công.', 'primary', true);
            setTimeout(() => {
                window.location.href = root;
            }, 500);
        } else {
            notify('Không thể xác nhận đơn hàng <br>vui lòng kiểm tra lại .', 'danger', true);
            $('#btnCheckout').prop('disabled', false);
        }
    });
}

function updateFulfillmentOrder(id) {
    var btn = document.getElementById('fulfillmentOrder' + id);
    var orderBox = btn.closest('.col-12');
    $.post(root + 'home/updateFulfillmentOrder', {
        id: id,
        csrf_token: $('#csrf_token').val()
    }, function (res) {
        // var res = JSON.parse(data);
        $('#csrf_token').val(res.key);
        if (res.status) {
            notify('Đơn hàng đã được hoàn thành.', 'primary', true);
            if (orderBox) {
                orderBox.remove(); // Xóa khỏi DOM
            }
        } else {
            notify('Không thể xác nhận hoàn thành <br>vui lòng kiểm tra lại .', 'danger', true);
        }
    });
}
function linkCheckout(id = 0) {
    if (id > 0 || id != 0) {
        addCart(id);
    }
    setTimeout(() => {
        window.location.href = root + 'xac-nhan-don-hang'
    }, 500);
}

function removeAllCart() {
    $.post(root + 'home/removeAllCart', {
        csrf_token: $('#csrf_token').val()
    }, function (res) {
        // var res = JSON.parse(data);
        $('#csrf_token').val(res.key);
        if (res.status) {
            notify('Giỏ hàng đã được xóa.', 'primary', true);
            openCart()
        } else {
            notify('Không thể hoàn thành <br>vui lòng kiểm tra lại .', 'danger', true);
        }
    });
}
function saveForWaiting() {
    $.post(root + 'home/addForWaiting', {
        csrf_token: $('#csrf_token').val()
    }, function (res) {
        // var res = JSON.parse(data);
        $('#csrf_token').val(res.key);
        if (res.status) {
            notify(res.msg, 'primary', true);
            $('#count-hold').html(parseInt(res.countHold));
            window.location.href = root
        } else {
            notify('Không thể lưu giỏ hàng chờ, Vui lòng liên hệ quản lý.', 'danger', true);
        }
    });
}

function showHold() {
    $('#wishlist_side').load(root + 'home/viewHoldCart', {
        csrf_token: $("#csrf_token").val()
    })
}
function getForWaiting(id) {
    $.post(root + 'home/getForWaiting', {
        hold_id: id,
        csrf_token: $('#csrf_token').val()
    }, function (res) {
        // var res = JSON.parse(data);
        $('#csrf_token').val(res.key);
        if (res.status) {
            notify(res.msg, 'primary', true);
            openCart();
            closeWishlist()
        } else {
            notify('Không thể đưa đơn hàng vào giỏ hàng, Vui lòng liên hệ quản lý.', 'danger', true);
        }
    });
}

function removeAllHold() {
    $.post(root + 'home/removeAllHold', {
        csrf_token: $('#csrf_token').val()
    }, function (res) {
        // var res = JSON.parse(data);
        $('#csrf_token').val(res.key);
        if (res.status) {
            notify(res.msg, 'primary', true);
            window.location.href = root
        } else {
            notify('Không thể xóa hết giỏ hàng chờ, Vui lòng liên hệ quản lý.', 'danger', true);
        }
    });
}

function editCartItem(id, key) {
    $.post(root + 'home/selectedCardItem', {
        productId: id,
        cartKey: key,
        csrf_token: $('#csrf_token').val()
    }, function (res) {
        $('#csrf_token').val(res.key);
        if (res.status) {
            if (res.selected && res.product) {
                let p = (res.product);
                let cartItem = (res.selected);
                console.log(cartItem);
                console.log(p);

                //
                let cartIndex = key;
                let price = p['price'];
                let qtyNow = cartItem.amount || 1;
                let noteNow = cartItem.note || '';
                let selectedSize = (typeof cartItem.size !== 'undefined') ? cartItem.size : p.is_size;
                let selectedToppings = cartItem.topping || [];
                let url_global = 'https://61579.net/assets/uploads/product/';
                let toppingPrice = 0;
                if(selectedToppings && selectedToppings.length > 0) {
                    for(let i = 0; i < selectedToppings.length; i++) {
                        let findTopping = p.toppings.find(t => t.id == selectedToppings[i].id);
                        if(findTopping) {
                            toppingPrice += Number(findTopping.price) * Number(selectedToppings[i].qty);
                        }
                    }
                }
                let str = '';
                str += '<div class="row">'
                str += '<div class="col-lg-12">'
                str += '<div class="media-banner plrb-0 b-g-white1 border-0">'
                str += '<div class="media-banner-box">'
                str += '<div class="media">'
                str += '<img src="' + url_global + p['image'] + '" class="img-fluid  w-25" alt="product">'
                str += '<div class="media-body">'
                str += '<div class="media-contant">'
                str += '<div>'
                str += '<div class="product-detail">'
                str += '<h3>' + p['name'] + '</h3>'
                str += '<h6 class="fm-number" id="txtPrice">' + (Number(price) + Number(p.price_size[selectedSize]) + Number(toppingPrice)) + '</h6>'
                str += '<div id="qtyProduct">'
                str += '<div class="qty-box pt-2">'
                str += '<div class="input-group">'
                str += '<button class="qty-minus"></button>'
                str += '<input class="qty-adj form-control" type="number" value="' + qtyNow + '" id="qtyItem' + id + '"/>'
                str += '<button class="qty-plus"></button>'
                str += '</div>'
                str += '</div>'
                str += '</div>'
                str += '</div>'
                str += '</div>'
                str += '</div>'
                str += '</div>'
                
                str += '</div>'
                str += '</div>'
                str += '<div class="delivery-detail-contian">'
                str += '<div class="input-group">'
                str += '<input type="text" id="note" class="form-control" placeholder="Ghi chú" value="' + noteNow.replace(/"/g, '&quot;') + '">'
                str += '</div>'
                str += '</div>'
                //
                str += '<hr>';
                str += '<form class="size-new" name="selectSize">'
                str += '<input type="hidden" value="' + Number(p.price_size[selectedSize]) + '" id="sizePriceNow" />'


                $.each(p.price_size, function (key, value) {
                    if (value != '') {
                        str +=
                            '<div class="card-product-option-item custom-radio mb-0" ><input type="radio" value="' +
                            key + '" ' + (key == selectedSize ? 'checked' : '') +
                            ' name="sizeProduct" id="size' + key + '" onclick="selectSizeProduct()"'
                        str += 'class="size-radio-input" data-size="' + value + '"> <label for="size' +
                            key + '" class="size-radio-label p-1">'
                        str += '<div class="size-radio-content">'
                        str += '<p class="size-name"> Size ' + key + ': </p>'
                        str += '<p class="size-price"> <span class="fm-number">' + (Number(p['price']) + Number(value)) + '</span>đ </p>'
                        str += '</div>'
                        str += '</label>'
                        str += '</div>'
                    }
                });

                str += '</form>'

                if (p.toppings && p.toppings.length && p.limit_topping > 0) {
                    let options = p.toppings
                    str += '<hr>';
                    str += '<div class="collection-collapse-block">';
                    str +=
                        '<h3 class="collapse-block-title mt-0">Chọn topping (Tối đa <span id="limitTopping">' +
                        p.limit_topping + '</span> món)</h3>';
                    str += '<div class="collection-collapse-block-content">';
                    str += '<div class="collection-brand-filter px-2">';
                    str += '<form id="listToppingCheck">';
                    for (let i = 0; i < options.length; i++) {
                        let findTopping = selectedToppings.find(t => t.id == options[i].id);
                        let isChecked = !!findTopping;
                        str +=
                            '<div class="custom-control custom-checkbox  form-check collection-filter-checkbox d-flex justify-content-between align-items-center">';
                        str += '<div>';
                        str +=
                            '<input type="checkbox" class="custom-control-input form-check-input" onclick="selectItem(' +
                            i + ',' + options[i].id + ')" id="topping' + i + '" value="' + options[i].id + '" ' + (isChecked ? 'checked' : '') + '>';
                        str += '<label class="custom-control-label form-check-label" for="topping' + i + '">' +
                            options[i].name + '</label>';
                        str += '</div>';
                        if (options[i].isMulti && options[i].isMulti > 0) {
                            let qtyVal = findTopping ? Number(findTopping.qty) : 1;
                            str += '<div class="qty-box">';
                            str += '<div class="input-group border-0">';
                            str +=
                                '<button type="button" class="btn quantity-left-minus add-to-cart-qty pl-1" ' + (isChecked ? 'disabled' : '') + '></button>';
                            str +=
                                '<input class="form-control input-number qty-input border-0" type="number" value="' + qtyVal + '" min=1 max="' + options[i].saleableQty + '" />';
                            str +=
                                '<button type="button" class="btn quantity-right-plus add-to-cart-qty pl-1" ' + (isChecked ? 'disabled' : '') + '></button>';
                            str += '</div>';
                            str += '</div>';
                        }

                        str +=
                            '<div><label class="custom-control-label form-check-label text-capitalize" for="topping' +
                            i + '"><span class="fm-number" id="txtToppingPrice' + i + '">' + options[i].price +
                            '</span>đ</label></div>';
                        str += '</div>';
                    }
                    str += '</form>';
                }
                str += '<div class="form-group mx-sm-3 mt-5 d-flex justify-content-around">';
                str +=
                    '<button type="button" class="btn btn-theme btn-normal btn-sm" onclick="updateCartTopping(\'' + (cartIndex) + '\',' + p.id + ')">Cập nhật</button>';
                str +=
                // làm sao tắt modal bằng nút này
                    '<button type="button" class="btn btn-theme btn-normal bg-secondary btn-sm" onclick="$(\'#exampleModal\').modal(\'hide\')">Hủy</button>';
                str += '</div>';

                $('#quickViewOrderProduct').children().remove();
                $('#quickViewOrderProduct').append(str);
                $('.fm-number').number(true, 0);
                $('#exampleModal').modal('show');
            }
        } else {
            notify('Không thể chỉnh sửa, Vui lòng liên hệ quản lý.', 'danger', true);
        }
    });
}

function selectItem(ind, id) {
    var itemCheck = document.getElementById('topping' + ind);
    var count = 0;
    var priceRealTime = parseInt($('#txtPrice').html().replace(',', ''));
    let toppingPrice = parseInt($('#txtToppingPrice' + ind).html().replace(',', ''));
    //lấy sl topping selected
    let qtyValue = 1
    let $checkbox = $('#topping' + ind);
    let $toppingContainer = $checkbox.closest('.collection-filter-checkbox');
    let $qtyBox = $toppingContainer.find('.qty-box');
    if ($qtyBox.length) {
        qtyValue = Number($qtyBox.find('.qty-input').val());
    }
    if (itemCheck.checked == true) {
        // check 
        $('#txtPrice').html(priceRealTime + (toppingPrice * qtyValue)).number(true, 0)
        if ($qtyBox.length) {
            $qtyBox.find('.qty-input').prop('disabled', true);
            $qtyBox.find('.add-to-cart-qty').prop('disabled', true);
        }
        for (var i = 0; i < $("form#listToppingCheck :checkbox").length; i++) {
            if (document.getElementById('topping' + i) != null) {
                itemCheck = document.getElementById('topping' + i);
                if (itemCheck.checked) {
                    count++;
                }
            }
        }
        let limitTopping = $('#limitTopping').html();
        if (count >= limitTopping) {
            for (var i = 0; i < $("form#listToppingCheck :checkbox").length; i++) {
                if (document.getElementById('topping' + i) != null) {
                    itemCheck = document.getElementById('topping' + i);
                    if (!itemCheck.checked) {
                        document.getElementById('topping' + i).disabled = true;
                    }
                }
            }
        }
        // if(count == $("tbody tr").length) {
        //     $('#selectAllItems').parent('span').addClass('checked');
        //     document.getElementById('selectAllItems').checked = true;
        // }
    } else {
        $('#txtPrice').html(priceRealTime - (toppingPrice * qtyValue)).number(true, 0)
        if ($qtyBox.length) {
            $qtyBox.find('.qty-input').prop('disabled', false);
            $qtyBox.find('.add-to-cart-qty').prop('disabled', false);
        }
        let countChecked = 0
        for (var i = 0; i < $("form#listToppingCheck :checkbox").length; i++) {
            if (document.getElementById('topping' + i) != null) {
                itemCheck = document.getElementById('topping' + i);
                if (itemCheck.checked) {
                    countChecked++;
                }
            }
        }
        let limitTopping = $('#limitTopping').html();
        if (countChecked <= limitTopping) {
            for (var i = 0; i < $("form#listToppingCheck :checkbox").length; i++) {
                if (document.getElementById('topping' + i) != null) {
                    itemCheck = document.getElementById('topping' + i);
                    if (!itemCheck.checked) {
                        document.getElementById('topping' + i).disabled = false;
                    }
                }
            }
        }
    }
}

function selectSizeProduct() {
    var priceRealTime = parseInt($('#txtPrice').html().replace(',', ''));
    var priceSize = $('input[name="sizeProduct"]:checked').data('size');
    var sizePriceNow = parseInt($('#sizePriceNow').val());

    $('#txtPrice').html(priceRealTime - sizePriceNow + priceSize).number(true, 0)
    $('#sizePriceNow').val(priceSize)
}
function updateCartTopping(cartIndex, productId) {
    $.post(root + 'home/removeCart', {
        id: productId,
        index: cartIndex,
        csrf_token: $('#csrf_token').val()
    }, function (res) {
        $('#csrf_token').val(res.key);
        if (res.status) {
            addCart(productId);
            openCart();
        } else {
            notify('Không thể update sản phẩm.', 'danger', true);
        }
    });
}

