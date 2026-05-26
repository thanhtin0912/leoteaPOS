<!-- thank-you section start -->
<script>
function formatCurrency(value) {
    let amount = Number(value);
    return new Intl.NumberFormat('en-US').format(amount);
}

function updateSummaryValues() {
    let rawValue = $('#subTotalPrice').text();
    let subTotal = Number(rawValue.replace(/\D/g, ''));
    let delivery = $('input[name="orderType"]:checked').val();
    let store = <?php echo json_encode($store); ?>;

    if (delivery === 'Delivery' && subTotal < store['condition']) {
        let shippingFee = Number(store['shippingfee']);
        let discount = Number($('#discountPrice').text().replace(/\D/g, '')) || 0;
        let grandTotal = subTotal - discount + shippingFee;
        $('#shippingPrice').html(formatCurrency(shippingFee));
        $('#finalPrice').html(formatCurrency(grandTotal));
        
    } else {
        $('#shippingPrice').html(0);
        $('#finalPrice').html(formatCurrency(subTotal));
    }
    addCoupon()
}

$(document).on('change', 'input[name="orderType"]', function() {
    updateSummaryValues();
});

$(function() {
    updateSummaryValues();
});

function addCoupon() {
    let rawValue = $('#subTotalPrice').text();
    let subTotal = Number(rawValue.replace(/\D/g, ''));
    let discountPrice = $('#adddiscountPrice').val().trim() || '0';
    discountPrice = Math.floor(discountPrice / 1000) * 1000;
    if(subTotal < discountPrice) {
        notify('KM không thể lớn đơn hàng.', 'danger', true);
        return;
    }
    let grandTotal = subTotal - Number(discountPrice);
    let finalPrice = grandTotal + Number($('#shippingPrice').text().replace(/\D/g, ''));
    $('#discountPrice').html(formatCurrency(discountPrice));
    $('#finalPrice').html(formatCurrency(finalPrice));
    $('#adddiscountPrice').val(discountPrice);
    
}
function selectCoupon(code, element) {
    let rawData = <?php echo json_encode($coupons); ?>;
    let dataCoupon = Object.values(rawData);
    let selected = dataCoupon.find(c => c.code === code);
    if (selected) {
        if (selected.type == 2) {
            let subTotal = Number($('#subTotalPrice').text().replace(/\D/g, ''));
            if(Number(selected.condition) < subTotal) {
                let discountAmount = Math.round(subTotal * (Number(selected.discount) / 100));
                discountAmount = Math.floor(discountAmount / 1000) * 1000;
                $('#adddiscountPrice').val(discountAmount);
                $('#discountName').html(selected.name);
                couponSelect(element);
            } else {
                notify('Đơn hàng không đủ điều kiện sử dụng mã giảm giá.', 'danger', true);
                return;
            }
            
        }
        if(selected.type == 1) {
            let subTotal = Number($('#subTotalPrice').text().replace(/\D/g, ''));
            if(Number(selected.condition) < subTotal) {
                $('#adddiscountPrice').val(selected.discount);
                $('#discountName').html(selected.name);
                couponSelect(element);
            } else {
                notify('Đơn hàng không đủ điều kiện sử dụng mã giảm giá.', 'danger', true);
                return;
            }
            
        }
        if(selected.type == 3) {
            let amount = Number($('#amount').text().replace(/\D/g, ''));
            let subTotal = Number($('#subTotalPrice').text().replace(/\D/g, ''));
            let condition = Number(selected.condition) + Number(selected.discount)
            if(condition <= amount) {
                // chia lấy số nguyên
                let numberOfFreeItems = Math.floor(amount / Number(condition)) || 1;
                let items = <?php echo json_encode($cart); ?>;
                // mình muôn tìm ra sản phẩm có giá thấp nhất trong giỏ hàng để áp dụng giảm giá số ly
                let minPriceItem = items.reduce((minItem, currentItem) => {
                    return currentItem.totalPrice < minItem.totalPrice ? currentItem : minItem;
                }, items[0]);
                
                let discountAmount = Number(selected.discount) * numberOfFreeItems * (Number(minPriceItem.totalPrice)/Number(minPriceItem.amount));
                $('#adddiscountPrice').val(discountAmount);
                $('#discountName').html(selected.name);
                couponSelect(element);
            } else {
                notify('Đơn hàng không đủ điều kiện sử dụng mã giảm giá.', 'danger', true);
                return;
            }
            
        }
        $('#couponCode').val(code);
        addCoupon()
    }
}
function couponSelect(element) {
    // 1. Tìm tất cả các thẻ có class 'coupon-item' và xóa class 'active'
    document.querySelectorAll('.coupon-item').forEach(li => {
        li.classList.remove('active');
    });
    // 2. Thêm class 'active' vào thẻ vừa click
    element.classList.add('active');
}
</script>
<style>
.checkout-summary-card {
    background: linear-gradient(180deg, #ffffff 0%, #fffdf8 100%);
    border: 1px solid rgba(233, 160, 83, 0.2);
    border-radius: 18px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
}

.checkout-summary-label {
    font-size: 0.95rem;
    color: #6c757d;
    margin-bottom: 0;
}

.checkout-summary-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 42px;
    height: 42px;
    border-radius: 999px;
    background: #fff4e6;
    color: #d97706;
    font-weight: 700;
}

.checkout-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.9rem 0;
    border-bottom: 1px dashed #e9ecef;
    font-size: 1rem;
    color: #495057;
}

.checkout-summary-row:last-child {
    border-bottom: 0;
}

.checkout-summary-row strong {
    color: #111827;
    font-weight: 700;
}

.checkout-summary-total {
    margin-top: 0.75rem;
    padding-top: 1rem;
    border-top: 2px solid #f59e0b;
}

.checkout-summary-total span,
.checkout-summary-total strong {
    font-size: 1.1rem;
}

.checkout-summary-total strong {
    color: #b45309;
}
.product-offer .offer-contain ul li {
    display: contents ! important;
    padding: 2rem 5rem;
}
.product-offer .offer-contain ul li .code-lable {
    padding: 0.5rem 1rem;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 10px;
}
.coupon-item.active span{
    color: #00baf2 !important;
    background-color: rgba(0, 186, 242, 0.08) !important;
    border: 1px dashed #00baf2 !important;
}
.offer-contain ul {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.offer-contain .coupon-item {
    background-color: #f1faf2;
    border: 1px dashed #4caf50;
    border-radius: 6px;
    padding: 4px 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    /* transition: all 0.2s ease; */
}

.offer-contain .coupon-item:hover {
    background-color: #e2f3e4;
    border-color: #388e3c;
    /* transform: translateY(-2px); */
}

.offer-contain .code-lable {
    color: #2e7d32;
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.5px;
}
</style>
<section class="section-big-py-space light-layout">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="success-text">
                    <h2>XÁC NHẬN HÓA ĐƠN</h2>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->


<!-- order-detail section start -->
<section class="section-big-py-space mt-5 b-g-light mb-5">
    <div class="custom-container">
        <div class="row">
            <div class="col-lg-6">
                <div class="product-order">
                    <h3>Chi tiết đơn hàng</h3>
                    <?php $total = 0;$amount = 0;?>
                    <?php foreach ($cart as $key => $v): ?>
                    <div class="row product-order-detail py-2">
                        <div class="col-3"><img src="<?=GLOBAL_URL.$v->image ?>" alt="" class="img-fluid "></div>
                        <div class="col-4 order_detail">
                            <div>
                                <h4><?= $v->name; ?> <?php if ($v->size != '') { echo "(".$v->size.")";}?></h4>
                                <?php if($v->priceTopping > 0) { ?>
                                <h5><?= $v->topping; ?></h5>
                                <?php } ?>
                                <?php if($v->note!='' || $v->note!= NULL) { ?>
                                <h5 style="font-style: italic">*Note: <?= $v->note; ?></h5>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-1 order_detail">
                            <div>
                                <h4>SL</h4>
                                <h5><?= $v->amount; ?></h5>
                            </div>
                        </div>
                        <div class="col-3 order_detail justify-content-end">
                            <div>
                                <h4>Đơn giá</h4>
                                <h5><?php echo number_format($v->totalPrice); ?></h5>
                            </div>
                        </div>
                        <div class="col-1 order_detail justify-content-end">
                            <div>
                                <h5>
                                    <a href="javascript:void(0)" onclick="removecart(<?=$v->id;?>,<?=$key;?>)"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-trash-2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                            </path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg></a>
                                </h5>
                            </div>
                        </div>
                    </div>
                    <?php $amount = $amount + ($v->amount); ?>
                    <?php $total = $total + ($v->totalPrice); ?>
                    <?php endforeach ?>
                    <div class="checkout-summary-card p-4 mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h3 class="mb-0">Tổng quan đơn hàng</h3>
                            </div>
                            <div class="checkout-summary-badge" id="amount"><?= number_format($amount); ?></div>
                        </div>

                        <div class="checkout-summary-row">
                            <span>Tổng tiền</span>
                            <strong id="subTotalPrice"><?= number_format($total); ?></strong>
                        </div>
                        <div class="checkout-summary-row">
                            <span>Phí giao hàng</span>
                            <strong id="shippingPrice">0</strong>
                        </div>
                        <div class="checkout-summary-row">
                            <span id=discountName>Giảm giá</span>
                            <strong id="discountPrice">0</strong>
                        </div>
                        <div class="checkout-summary-row checkout-summary-total">
                            <span>Thành tiền</span>
                            <strong id="finalPrice"><?= number_format($total); ?></strong>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-6">
                <div class="row order-success-sec">
                    <?php if($coupons){ ?>
                    <div class="col-sm-12">
                        <h4>Khuyến mãi</h4>
                        <div class="input-group w-100">
                            <input type="text" id="adddiscountPrice" class="form-control" aria-label="Nhập chiết khấu"
                                placeholder="Nhập chiết khấu" disabled>
                            <button class="btn btn-danger" onclick="addCoupon()"><i class="fa fa-tag"></i> Áp
                                dụng</button>
                        </div>
                        <div class="py-3">
                            <input type="hidden" id="couponCode" value="">
                            <div class="product-offer pb-2">
                                <div class="offer-contain">
                                    <ul>
                                        <?php foreach ($coupons as $c) { ?>
                                        <li onclick="selectCoupon('<?=$c->code?>', this)" class="coupon-item" style="cursor: pointer;">
                                            <span class="code-lable"><?=$c->code?></span>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="col-sm-12">
                        <h4>Loại đơn hàng</h4>
                        <form class="size-new py-3">
                            <div class="card-product-option-item custom-radio mb-0">
                                <input type="radio" value="Eatin" checked name="orderType" id="orderType1"
                                    class="size-radio-input" data-size="0">
                                <label for="orderType1" class="size-radio-label p-1">
                                    <div class="size-radio-content">
                                        <p class="size-name">Eatin</p>
                                    </div>
                                </label>
                            </div>
                            <div class="card-product-option-item custom-radio mb-0">
                                <input type="radio" value="Takeaway" name="orderType" id="orderType2"
                                    class="size-radio-input" data-size="0">
                                <label for="orderType2" class="size-radio-label p-1">
                                    <div class="size-radio-content">
                                        <p class="size-name">Takeaway</p>
                                    </div>
                                </label>
                            </div>
                            <div class="card-product-option-item custom-radio mb-0">
                                <input type="radio" value="Delivery" name="orderType" id="orderType3"
                                    class="size-radio-input" data-size="0">
                                <label for="orderType3" class="size-radio-label p-1">
                                    <div class="size-radio-content">
                                        <p class="size-name">Delivery</p>
                                    </div>
                                </label>
                            </div>

                        </form>
                    </div>
                    <div class="col-sm-12">
                        <h4>Hình thức thanh toán</h4>
                        <form class="size-new py-3">
                            <div class="card-product-option-item custom-radio mb-0">
                                <input type="radio" value="1" checked name="orderPayment" id="orderPayment1"
                                    class="size-radio-input" data-size="0">
                                <label for="orderPayment1" class="size-radio-label p-1">
                                    <div class="size-radio-content">
                                        <p class="size-name">Tiền mặt</p>
                                    </div>
                                </label>
                            </div>
                            <div class="card-product-option-item custom-radio mb-0">
                                <input type="radio" value="2" name="orderPayment" id="orderPayment2"
                                    class="size-radio-input" data-size="0">
                                <label for="orderPayment2" class="size-radio-label p-1">
                                    <div class="size-radio-content">
                                        <p class="size-name">Chuyển khoản</p>
                                    </div>
                                </label>
                            </div>

                        </form>
                    </div>
                    <div class="col-sm-12">
                        <h4>ghi chú</h4>
                        <textarea rows="3" id="note" class="form-control"></textarea>
                    </div>
                    <div class="col-sm-12 payment-mode">

                        <div class="delivery-sec">
                            <h2><?= date("Y-m-d H:m",time());?></h2>
                        </div>
                    </div>
                    <div class="col-12 pt-5 text-center">
                        <?php if($this->session->userdata('staffName')){ ?>
                        <button class="btn btn-sm bg-danger btn-normal" onclick="saveForWaiting();">Lưu đơn</button>
                        <button class="btn btn-normal btn-sm" onclick="checkout();" id="btnCheckout">Xác nhận
                            đơn</button>

                        <?php }else{ ?>
                        <div class="title6">
                            <h4>Vui lòng vào ca để đc đặt hàng</h4>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->