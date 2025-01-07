
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Shipping detail</h2>
            <p>Details for Order ID: ORDER_ID</p>
        </div>
    </div>
    <div class="card">
        <header class="card-header">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                    <span><i class="material-icons md-calendar_today"></i><b>DATE</b></span><br />
                    <small class="text-muted">Order ID: ORDER_ID</small>
                </div>
                <div class="col-lg-6 col-md-6 ms-auto text-md-end">
                
                        <button type="submit" class="btn btn-primary" name="update_status">Save</button>
                                    </div>
            </div>
        </header>
        <div class="card-body">
            <div class="row mb-50 mt-20 order-info-wrap">
                <div class="col-md-4">
                    <article class="icontext align-items-start">
                        <span class="icon icon-sm rounded-circle bg-primary-light">
                            <i class="text-primary material-icons md-person"></i>
                        </span>
                        <div class="text">
                            <h6 class="mb-1">Customer</h6>
                            <p class="mb-1">
                                CUSTOMER_NAME<br />
                                CUSTOMER_EMAIL<br />
                                CUSTOMER_PHONE
                            </p>
                        </div>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="icontext align-items-start">
                        <span class="icon icon-sm rounded-circle bg-primary-light">
                            <i class="text-primary material-icons md-local_shipping"></i>
                        </span>
                        <div class="text">
                            <h6 class="mb-1">Order info</h6>
                            <p class="mb-1">
                                Status: ORDER_STATUS<br />
                                Shipping Address: SHIPPING_ADDRESS
                            </p>
                        </div>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="icontext align-items-start">
                        <span class="icon icon-sm rounded-circle bg-primary-light">
                            <i class="text-primary material-icons md-place"></i>
                        </span>
                        <div class="text">
                            <h6 class="mb-1">Deliver to</h6>
                            <p class="mb-1">
                                Address: DELIVERY_ADDRESS
                            </p>
                        </div>
                    </article>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-7">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="40%">Product</th>
                                    <th width="20%">Unit Price</th>
                                    <th width="20%">Quantity</th>
                                    <th width="20%" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div>
                                            <img src="PRODUCT_IMAGE" alt="PRODUCT_NAME" class="img-fluid" style="width: 100px; height: auto; object-fit: cover;">
                                        </div>
                                        PRODUCT_NAME
                                    </td>
                                    <td>RpUNIT_PRICE</td>
                                    <td>QUANTITY</td>
                                    <td class="text-end">RpTOTAL_PRICE</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="box shadow-sm bg-light">
                        <h6 class="mb-15">Tanggal Dikirim</h6>
                    </div>
</br>
</br>
                    <div class="box shadow-sm bg-light">
                        <h6 class="mb-15">Tanggal Diterima</h6>
                    </div>
                    <div class="h-25 pt-4">
                        <div class="mb-3">
                            <label>Nomor Resi</label>
                            <textarea class="form-control" name="notes" id="notes" placeholder="Type some note" >NOTES</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
