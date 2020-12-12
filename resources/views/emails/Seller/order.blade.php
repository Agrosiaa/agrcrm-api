<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

Dear {{ucwords($sellerUser['first_name'])}} {{ucwords($sellerUser['last_name'])}},<br>
Congratulations, Order has been placed at Agrosiaa . <br><br>

Order Details:<br>
Placed on : {{$orderedOn}}<br>
Estimated Delivery Date : {{$deliveryDate}}<br>
Delivery Method : {{$DeliveryTypeInfo['name']}}<br><br>

Your order will be delivered to:<br>
{{ $address->full_name}}<br>
{{ $address->mobile}}<br>
{{$address->flat_door_block_house_no}},{{$address->name_of_premise_building_village}},<br>
{{$address->area_locality_wadi}}, {{$address->road_street_lane}},<br>
{{$address->at_post}}, {{$address->taluka}},<br>
{{$address->district}}, {{$address->state}}, {{$address->pincode}}<br>
India<br>
<br>
@for($i=0;$i < count($orderSeller); $i++)
    Order : AGR{{$orderSeller[$i]['order_format']}}<br>
    &nbsp;&nbsp;        {{$orderSeller[$i]['product_name']}}<br>
    &nbsp;&nbsp;        {{ucwords($orderSeller[$i]['brand_name'])}}<br>
    Quantity : {{$orderSeller[$i]['quantity']}}
    <br>
    @if($orderSeller[$i]['is_configurable'] == true)
        Item Subtotal : Rs. {!!  ($orderSeller[$i]['discounted_price'] * ($orderSeller[$i]['length'] * $orderSeller[$i]['width'])) * $orderSeller[$i]['quantity']!!}<br>
    @else
        Item Subtotal : Rs. {!!  $orderSeller[$i]['discounted_price'] * $orderSeller[$i]['quantity']!!}<br>
    @endif
    Shipping and Handling Charges :Rs. {{$DeliveryTypeInfo['amount']}}<br><br><br>

@endfor

Order Total : Rs.{{$total}}<br>
<br>
If you need any assistance or have any questions, feel free to send us an email on grievances@agrosiaa.com.

Regards,<br>
Agrosiaa Team<br>
www.agrosiaa.com<br>
<br>
<br>
NOTE: This email was sent from a notification-only email address that cannot accept incoming email. Please do not reply to this message.
<br>

</body>
</html>
