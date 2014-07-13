Reckless - Realtime Cart Abandonment Detection and Prevention
About Reckless Data

We are building accessible decision support tools; to empower retailers with tools like those of Amazon.
Reckless Prediction

This Magento extension learns from your historic Magento operational data and predicts whether a given online customer or guest will checkout or not.
The system analyses user behaviour in realtime and predicts the checkout intention of the user.

Depending on the predicted checkout intent (Y/N), a personalised coupon code can be generated to either convert the leaving customer and prevent a cart abandonment; or increase the cart vale of the engaged customer. We also use external variables like holidays, weather etc. in our machine learning models.

By default, coupon codes are generated every 5 minutes for online customers and the coupon code gets displayed to the customer when he/she adds another item to their cart.
Why use Reckless Prediction ?

a) Increase Average Order Value: If the predicted checkout intent is a “YES” and the AOV for this customer is higher that the current quote, you can offer targeted promotion to increase the Cart value.

b) Increase conversion & reduce abandoned cart : If the predicted checkout intent is a “NO”, you can entice the customer to checkout by giving a time limited coupon code or free shipping.

c) Increase Lifetime value: If the predicted checkout intent is a “YES”, you can offer a coupon code, valid from another date and entice him to come back before the coupon expiry date.

Data Privacy

- Customer email is never used unless specified in the extension configuration
- Customer ID is the only information used for a customer. No name, address, billing or any other information is processed by Reckless.

Features

- Works for both logged in(registered) and guest customers
- Promotion Budget control with Coupon validity Days and maximum limit
- Customizable messages, coupon code/ discount percent/ and other basic configurations

Reporting

Report 1 : Promotions Report
Reports the predicted checkout intent for all the online customers, their quotes and the corresponding coupon code offered to the customer.
Access: Magento Admin Panel : Customers > Reckless Promotions

Report 2: Promotions redemption Report
Reports on a day basis, how many times the generated coupons have been redeemed, the total discounted value and the customer status along with the relevant order number.
Access: Magento Admin Panel : Reports-> Reckless Redemptions

**The daily discounted value can be limited.

Demo Store

> Store: http://magento.reckless.io
> Admin Back-end: http://magento.reckless.io/admin (Admin Username: admin Password: password1234)

Key Configuration ( Access: Magento Admin Panel : System > Configurations > Reckless Data > Predictions )

    API Key: Configured by Reckless Servers, Please do not change.
    Registered Domain: Configured by Reckless Servers, Please do not change.
    Enable Customer Email Sync : By default this is disabled for data privacy control.
    Max promotion budget in 24hrs: Max coupon value that you want to give out in 24 hours. (Can be 5% of the customer quote value).
    Promotion Code Prefix: Prefix for every coupon code created.
    Message to show to customer: This is a message that will be shown in the cart to the customer.

Con Jobs

1) reckless_prediction_heartbeat: Runs every 5 minutes to update online customers with predicted checkout intents and generates relevant coupon codes.
2) reckless_start_training : Runs every 30 minutes to train the prediction model in Reckless.io


This extension is free to download and use for up-to 1,000,000 rows for the first 6 months. For any queries please email hello@reckless.io and we are more than happy to help you out.
