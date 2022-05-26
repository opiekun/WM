define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
], function ($, wrapper, quote) {
    'use strict';
    return function (setShippingInformationAction) {
        function setCompany(address, company) {
            var attribute = address.customAttributes ? address.customAttributes.find(
                function (element) {
                    return element.attribute_code === 'company';
                }
            ) : null;
            var companyToSet =  attribute ? attribute.value : company;

            if (address['company'] == undefined) {
                address['company'] = companyToSet;
            }


            return companyToSet;
        }

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var company = setCompany(quote.shippingAddress());
            setCompany(quote.billingAddress(), company);

            return originalAction();
        });
    };
});
