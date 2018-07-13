![alt text](http://www.wayoutconsulting.it/annunci/wp-content/uploads/2018/05/qapla.jpg)

#Qapla' API REST PHP Class (Beta)

This helpfull class allow you to use our API REST friendly.

You need just **download** this **class** and use it! 

An example below:

```PHP
/*
* Inizialize class
*/
$api = new QaplaApiRest([
                  'apiKey'=>'1111aa111b1111ccc111d111e11f11g11h11i1jj11111111kk1111l11mmm1nn1'
              ]);
$result = $api->getShipping('orderReference','999999999');             
```

##Public method

| **Method** | **Description** |
|:----------:|:---------------:|
| _getShipping()_ | Get shipment status by tracking number or order reference |
| _getShippingMultiple()_ | Get Shippings by date or day |
| _pushShipping()_ | Push one shipping in the Qapla' system |
| _pushShippingMultiple()_ | Push one or more shippings in the Qapla' system |
| _deleteShipping()_ | Delete a shipment via trackingNumber |
| _pushOrder()_ | Push one order into Qapla' system |
| _pushOrderMultiple()_ | Push one or more order in the Qapla' system |
| _getOrdersMultiple()_ | Get Orders by date or day |
| _getCouriers()_ | Get the list of all current couriers |
| _getCredits()_ | Get the number of current account premium credits |
