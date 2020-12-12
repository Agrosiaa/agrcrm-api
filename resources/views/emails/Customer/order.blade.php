<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

Dear @if(array_key_exists('first_name',$user)) {{$user['first_name']}} {{$user['last_name']}} @else {{ $customer->full_name}} @endif,<br>
Thank you for your order. <br><br>

Order Details:<br>
Placed on : {{$orderedOn}}<br>
Arriving : {{$deliveryDate}}<br>
<br>
Your delivery method : {{$deliveryName}}<br><br>

Your order will be sent to:<br>
{{ $customer->full_name}}<br>
{{$customer->flat_door_block_house_no}},{{$customer->name_of_premise_building_village}},<br>
{{$customer->area_locality_wadi}}, {{$customer->road_street_lane}},<br>
{{$customer->at_post}}, {{$customer->taluka}},<br>
{{$customer->district}}, {{$customer->state}}, {{$customer->pincode}}<br>
India<br>
<br>
@for($i=0;$i < count($orderIds); $i++)
    Order : AGR{{$orderIds[$i]}}<br>
    &nbsp;&nbsp;        {{ucwords($productNames[$i])}}<br>
    &nbsp;&nbsp;        {{ucwords($productBrands[$i])}}<br>
    <br>
    Total Before Tax : Rs.{{$totalBeforeTax[$i]}}<br>
    @if($igst_applied[$i] == true)
        IGST ({{$tax_rate[$i]}}%) : {{$taxAmount[$i]}}<br>
    @else
        CGST ({!! $tax_rate[$i]/2 !!}%) : {!! $taxAmount[$i]/2 !!}<br>
        SGST ({!! $tax_rate[$i]/2 !!}%) : {!! $taxAmount[$i]/2 !!}<br>
    @endif
    Item Subtotal : Rs. {{$productSubtotals[$i]}}<br>
    Shipping and Handling Charges :Rs. {{$shippingCharges}}<br><br><br>
@endfor

{{--@if($couponDiscount!=0)
    Coupon Discount: Rs.{{$couponDiscount}}<br>
@endif--}}

Order Total : Rs.{{$grandTotal}}<br>

<br>
Some products have a limited quantity available for purchase. Please see the product’s Detail Page for the available quantity.We’ll send a confirmation mail when your order is shipped.
Do visit us at www.agrosiaa.com for your other requirements. &nbsp;&nbsp;&nbsp;
<br>
If you need any assistance or have any questions, feel free to send us an email on customercare@agrosiaa.com, or call our Customer Care on 9800012345<br><br>


Regards,<br>
Agrosiaa Team<br>
www.agrosiaa.com<br>
<br>
<br>
NOTE: This is an automatically generated email, Please do not reply to this email.<br>

</body>
</html>
