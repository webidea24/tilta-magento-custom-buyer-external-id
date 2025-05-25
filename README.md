Example module to use a attribute of the customer for the external-buyer-id
================================================

This module demonstrates how to implement a custom buyer-external-id for the [Tilta Payment module](https://github.com/tilta-io/tilta-magento-2-plugin) based on a customer attribute.

Please adjust the module to suit your specific requirements.

## Configuration

Please have a look into the file `etc/config.xml`.
There are the following configurations:

| Key                        | Description                                                                                                                                                                                                                                    |
|----------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| enabled                    | Enable this module                                                                                                                                                                                                                             |
| customer_attribute         | The code of the attribute of the customer, which contains the value for the buyer-external-id. This must be unique across all buyers.                                                                                                          |
| throw_exception_if_missing | if set to 1 an exception got thrown if the attribute does not contains any value. if this is set, it will break the creating facility functionality. If set to 0 and no value is found, the original generated buyer-external-id will be used. |

It is recommended to implement a settings page within the admin UI to configure these values.

## Compatibility & Maintain

This example module is compatible with the payment module starting from version 1.0.1.
The observed event will continue to be used in future releases.

This module will no longer be maintained, and we cannot guarantee future compatibility.

This extension will not be officially released, as this and similar functionality will be integrated directly into the payment module in a future version.
