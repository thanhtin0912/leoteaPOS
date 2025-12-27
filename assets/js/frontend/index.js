
$(document).ready(function() {
    showcart();
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
        }, function(res) {
            $('#csrf_token').val(res.key);
            if(res.status) {
                notify('Đăng nhập thành công.', 'primary', true);
                window.location.href = root
            } else {
                notify('Thông tin đăng nhập không đúng, vui lòng kiểm tra.', 'danger', true); 
            }
            
        });
    }
}

function notify (ms, type, status){
    //primary, secondary, success, warning, info, danger
    if(status) {
        $.notify({
            icon: 'fa fa-check',
            title: 'Vui lòng đợi!',
            message: ms
        },{
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

function addCart(id){
    var toppingSelected = [];
    for(var i=0;i<$("form#listToppingCheck :checkbox").length;i++){
        if(document.getElementById('topping'+i)!=null){
            itemCheck = document.getElementById('topping'+i);
            if(itemCheck.checked) {
                let $checkbox = $('#topping' + i);
                let $toppingContainer = $checkbox.closest('.collection-filter-checkbox');
                let $qtyBox = $toppingContainer.find('.qty-box');
                let qtyValue = 1
                if ($qtyBox.length) { 
                    qtyValue = Number($qtyBox.find('.qty-input').val());
                }
                let item = {
                    id: $('#topping'+i).val(),
                    qty: qtyValue
                };
                toppingSelected.push(item);
            }
        }
    }
    let size = $('input[name="sizeProduct"]:checked').val();
    let amount = $('#qtyItem'+id).val();
    let note = $('#note').val();

    if (!toppingSelected.length) {
        toppingSelected = toppingSelected.toString()
    }
    $.post(root+'home/addcart',{
        id:      id,
        size: size,
        amount: amount,
        note: note,
        topping: toppingSelected,
        csrf_token:     $('#csrf_token').val()
    },function(res){
        // var res = JSON.parse(data);
        $('#csrf_token').val(res.key);
        if(res.status) {
            notify('Đã thêm sản phẩm thành công.', 'primary', true);
            $('#exampleModal').modal('hide');
            $('#count-cart-product').html(parseInt(res.countCart));
        } else {
            notify('Sản phẩm không thể thêm vào giỏ hàng.', 'danger', true); 
        }
    });
}

function showcart(){
    $('#cart_side').load(root+'home/viewQuickCart', {
        csrf_token                 : $("#csrf_token").val()
    })
}

function removecart(id, index){
    closeCart();
    $.post(root+'home/removeCart',{
        id:      id,
        index:      index,
        csrf_token:     $('#csrf_token').val()
    },function(res){
        closeCart();
        // var res = JSON.parse(data)
        $('#csrf_token').val(res.key);
        if(res.status) {
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

function updateItemCart(id, index){
    let qty = $('#qtyItem'+index).val()
    closeCart();
    $.post(root+'home/updateItemCart',{
        id:      id,
        index:      index,
        qty: qty,
        csrf_token:     $('#csrf_token').val()
    },function(res){
        // var res = JSON.parse(data)
        $('#csrf_token').val(res.key);
        if(res.status) {
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

function checkout(){
    $('#btnCheckout').prop('disabled', true);
    let delivery = $('input[name="orderType"]:checked').val();
    let note =    $('#note').val()
    $.post(root+'home/checkoutCart',{
        delivery:      delivery,
        note: note,
        csrf_token:     $('#csrf_token').val()
    },function(res){
        // var res = JSON.parse(data);
        $('#csrf_token').val(res.key);
        if(res.status) {
            notify('Đơn hàng đã được đặt hàng thành công.', 'primary', true);
            setTimeout(() => {
                window.location.href = root + 'trang-thai-don-hang';
            }, 500);
        } else {
            notify('Không thể xác nhận đơn hàng <br>vui lòng kiểm tra lại .', 'danger', true); 
            $('#btnCheckout').prop('disabled', false);
        }
    });
}

function updateFulfillmentOrder(id){
    var btn = document.getElementById('fulfillmentOrder' + id);
    var orderBox = btn.closest('.col-12');
    $.post(root+'home/updateFulfillmentOrder',{
        id: id,
        csrf_token:     $('#csrf_token').val()
    },function(res){
        // var res = JSON.parse(data);
        $('#csrf_token').val(res.key);
        if(res.status) {
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
    if (id > 0  || id != 0) { 
        addCart(id);
    }
    setTimeout(() => {
        window.location.href = root + 'xac-nhan-don-hang' 
    }, 500);
}

function removeAllCart(){
    $.post(root+'home/removeAllCart',{
        csrf_token:     $('#csrf_token').val()
    },function(res){
        // var res = JSON.parse(data);
        $('#csrf_token').val(res.key);
        if(res.status) {
            notify('Giỏ hàng đã được xóa.', 'primary', true);
            openCart()
        } else {
            notify('Không thể hoàn thành <br>vui lòng kiểm tra lại .', 'danger', true); 
        }
    });
}
