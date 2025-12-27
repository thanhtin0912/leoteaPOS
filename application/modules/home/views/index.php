<script type="text/javascript">
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

    const target = e.target;

    const input = qtyBox.querySelector('.qty-adj');
    let value = parseInt(input.value) || 1;
    const min = parseInt(input.min) || 1;
    const max = parseInt(input.max) || Infinity;

    if (target.classList.contains('qty-minus')) {
        value = Math.max(min, value - 1);
        input.value = value;
    }
    if (target.classList.contains('qty-plus')) {
        value = Math.min(max, value + 1);
        input.value = value;
    }
});

function quickViewDetailProduct(id) {
    var isLogin = $('#checkUserInfo').val();
    if (isLogin) {
        var products = <?php echo json_encode($products) ?>;
        $.each(products, function(i, p) {
            if (id == p.id) {
                let str = ''
                let price = p['price']
                str += '<div class="row">'
                str += '<div class="col-lg-12">'
                str += '<div class="media-banner plrb-0 b-g-white1 border-0">'
                str += '<div class="media-banner-box">'
                str += '<div class="media">'
                str += '<img src="' + root + p['image'] + '" class="img-fluid  w-25" alt="product">'
                str += '<div class="media-body">'
                str += '<div class="media-contant">'
                str += '<div>'
                str += '<div class="product-detail">'
                str += '<h3>' + p['name'] + '</h3>'
                str += '<h6 class="fm-number" id="txtPrice">' + price + '</h6>'
                str += '<div id="qtyProduct">'
                str += '<div class="qty-box pt-2">'
                str += '<div class="input-group">'
                str += '<button class="qty-minus"></button>'
                str += '<input class="qty-adj form-control" type="number" value="1" id="qtyItem' + id + '"/>'
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
                str += '<input type="text" id="note" class="form-control" placeholder="Ghi chú">'
                str += '</div>'
                str += '</div>'
                //
                str += '<hr>';
                str += '<form class="size-new" name="selectSize">'
                str += '<input type="hidden" value="0" id="sizePriceNow" />'

                $.each(p.price_size, function(key, value) {
                    if (value != '') {
                        str +=
                            '<div class="card-product-option-item custom-radio mb-0" ><input type="radio" value="' +
                            key + '" ' + (key == p.is_size ? 'checked' : '') +
                            ' name="sizeProduct" id="size' + key + '" onclick="selectSizeProduct()"'
                        str += 'class="size-radio-input" data-size="' + value + '"> <label for="size' +
                            key + '" class="size-radio-label p-1">'
                        str += '<div class="size-radio-content">'
                        str += '<p class="size-name"> Size ' + key + ': </p>'
                        str += '<p class="size-price"> <span class="fm-number">' + (Number(p['price']) +
                            Number(value)) + '</span>đ </p>'
                        str += '</div>'
                        str += '</label>'
                        str += '</div>'
                    }
                });

                str += '</form>'
                //
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
                        str +=
                            '<div class="custom-control custom-checkbox  form-check collection-filter-checkbox d-flex justify-content-between align-items-center">';
                        str += '<div>';
                        str +=
                            '<input type="checkbox" class="custom-control-input form-check-input" onclick="selectItem(' +
                            i + ',' + options[i].id + ')" id="topping' + i + '" value="' + options[i].id + '">';
                        str += '<label class="custom-control-label form-check-label" for="topping' + i + '">' +
                            options[i].name + '</label>';
                        str += '</div>';
                        if (options[i].isMulti && options[i].isMulti > 0) {
                            str += '<div class="qty-box">';
                            str += '<div class="input-group border-0">';
                            str +=
                                '<button type="button" class="btn quantity-left-minus add-to-cart-qty pl-1"></button>';
                            str +=
                                '<input class="form-control input-number qty-input border-0" type="number" value="1" min=1 max="' +
                                options[i].saleableQty + '" />';
                            str +=
                                '<button type="button" class="btn quantity-right-plus add-to-cart-qty pl-1"></button>';
                            str += '</div>';
                            str += '</div>';
                        }

                        str +=
                            '<div><label class="custom-control-label form-check-label text-capitalize" for="topping' +
                            i + '"><span class="fm-number" id="txtToppingPrice' + i + '">' + options[i].price +
                            '</span>đ</label></div>';
                        str += '</div>';
                    }
                    str += '</div>';
                    str += '</div>';
                    str += '</div>';
                    str += '</form>';
                }
                str += '<div class="form-group mx-sm-3 mt-5 d-flex justify-content-around">';
                str +=
                    '<button type="submit" class="btn btn-theme btn-normal btn-sm" onclick="addCart(' +
                    id + ')">Thêm món</button>';
                str +=
                    '<button type="submit" class="btn btn-theme btn-normal bg-info btn-sm" onclick="linkCheckout(' +
                    id + ')">Tính tiền</button>';
                str += '</div>';

                $('#quickViewOrderProduct').children().remove();
                $('#quickViewOrderProduct').append(str);
                $('.fm-number').number(true, 0);
                return false;
            }

        })
        $('#exampleModal').modal('show');
    } else {
        openAccount();
        notify('Vui lòng đăng nhập để thực hiện đặt hàng.', 'danger', true);
    }
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
</script>
<section class="section-big-pt-space">
    <div class="collection-wrapper">
        <div class="custom-container">
            <div class="row">
                <div class="collection-content col">
                    <div class="page-main-content">
                        <div class="top-banner-wrapper">
                            <a href="javascript:void(0)">
                                <img src="<?=PATH_URL.DIR_UPLOAD_BANNER.$banner[0]->image ?>" class="img-fluid"
                                    alt="category"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!--title start-->
<div class="title8 section-big-pt-space">
    <h4>Bán chạy nhất</h4>
</div>
<!--title end-->

<section class=" ratio_asos product  section-big-pb-space">
    <div class="custom-container  addtocart_count ">
        <div class="row">
            <div class="col pr-0">
                <div class="theme-tab product ">
                    <div class="product-slide-6 product-m no-arrow">
                        <?php if($sales) { ?>
                        <?php foreach ($sales as $key => $v): ;?>
                        <div>
                            <div class="product-box ">
                                <div class="product-imgbox">
                                    <div class="product-front">
                                        <a href="javascript:void(0)" onclick="quickViewDetailProduct(<?=$v->id?>)">
                                            <img src="<?=PATH_URL.DIR_UPLOAD_PRODUCT.$v->image?>" class="img-fluid"
                                                alt="product">
                                        </a>
                                    </div>
                                </div>
                                <div class="product-detail detail-center1 pt-2">
                                    <a href="javascript:void(0)" onclick="quickViewDetailProduct(<?=$v->id?>)">
                                        <h6><?=$v->name ?></h6>
                                    </a>
                                    <span class="detail-price"><?=number_format($v->price) ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--product box end-->



<!--tab product-->
<section class="section-py-space">
    <div class="tab-product-main">
        <div class="tab-prodcut-contain">
            <ul class="tabs tab-title">
                <li class="current"><a href="tab-all">TẤT CẢ</a></li>
                <?php if($cates) { ?>
                <?php foreach ($cates as $key => $c): ;?>
                <li class=""><a href="tab-<?=$c->id ?>"><?=$c->name ?> </a></li>
                <?php endforeach ?>
                <?php } ?>
            </ul>
        </div>
    </div>
</section>
<!--tab product-->


<?php if($cates) { ?>
<!--product box start -->
<section class=" ratio_asos product  section-big-pb-space">
    <div class="custom-container  addtocart_count ">
        <div class="row">
            <div class="col pr-0">
                <div class="theme-tab product ">
                    <div class="tab-content-cls">
                        <div id="tab-all" class="tab-content active default">
                            <div class="collection-product-wrapper">
                                <div class="product-wrapper-grid product">
                                    <div class="row">
                                        <?php foreach ($products as $key => $p): ;?>
                                        <div class="col-xl-2 col-lg-3 col-md-3 col-12 col-grid-box">
                                            <div class="product-box d-flex d-md-block">
                                                <div class="product-imgbox w-responsive">
                                                    <div class="product-front">
                                                        <a href="javascript:void(0)"
                                                            onclick="quickViewDetailProduct(<?=$p->id?>)"> <img
                                                                src="<?=PATH_URL.$p->image ?>" class="img-fluid  "
                                                                alt="product"> </a>
                                                    </div>
                                                </div>
                                                <div
                                                    class="product-detail detail-center1 text-left text-md-center align-content-center w-100 pt-2">
                                                    <a href="javascript:void(0)"
                                                        onclick="quickViewDetailProduct(<?=$p->id?>)">
                                                        <h6><?=$p->name ?></h6>
                                                    </a>
                                                    <span class="detail-price"><?=number_format($p->price) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <?php foreach ($cates as $key => $c): ;?>
                        <div id="tab-<?=$c->id ?>" class="tab-content">
                            <div class="collection-product-wrapper">
                                <div class="product-wrapper-grid product">
                                    <div class="row">
                                        <?php foreach ($products as $key => $p): ;?>
                                        <?php if($c->id === $p->type) { ?>
                                        <div class="col-xl-2 col-lg-3 col-md-3 col-12 col-grid-box">
                                            <div class="product-box d-flex d-md-block">
                                                <div class="product-imgbox w-responsive">
                                                    <div class="product-front">
                                                        <a href="javascript:void(0)"
                                                            onclick="quickViewDetailProduct(<?=$p->id?>)"> <img
                                                                src="<?=PATH_URL.$p->image ?>" class="img-fluid  "
                                                                alt="product"> </a>
                                                    </div>
                                                </div>
                                                <div
                                                    class="product-detail detail-center1 text-left text-md-center align-content-center w-100 pt-2">
                                                    <a href="javascript:void(0)"
                                                        onclick="quickViewDetailProduct(<?=$p->id?>)">
                                                        <h6><?=$p->name ?></h6>
                                                    </a>
                                                    <span class="detail-price"><?=number_format($p->price) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php endforeach ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--product box end-->
<?php } ?>