<?php
class Home extends JI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        echo '<p></p><h3>Order Test Index</h3>';
        echo '<ul>';
        echo '<li><a href="'.base_url("runner/order/create_shipment").'">Create Shipment</a></li>';
        echo '<li><a href="'.base_url("runner/order/create_unpaid").'">Create Unpaid Order</a></li>';
        echo '<li><a href="'.base_url("runner/order/create_paid").'">Create Paid Order</a></li>';
        echo '<li><a href="'.base_url("runner/order/from_unpaid").'">From Unpaid Order</a></li>';
        echo '<li><a href="'.base_url("runner/order/from_unpaid").'">From Pending Order</a></li>';
        echo '<li><a href="'.base_url("runner/order/full_test").'">Full Test Order</a></li>';
        echo '<li><a href="'.base_url("runner/order/full_test_confirmed").'">Full Test Order: Confirmed By Buyer</a></li>';
        echo '<li><a href="'.base_url("runner/order/full_test_rejected").'">Full Test Order: Rejected By Buyer</a></li>';
        echo '</ul>';
    }
}
