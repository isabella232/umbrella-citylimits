# CityLimits.org Documentation

This document describes how the CityLimits.org theme was originally configured by [INN](http://nerds.investigativenewsnetwork.org/).

## Job listings

Make sure the WPJobBoard plugin is activated.

### To set up PayPal:

- From the WordPress dashboard, go to Settings (WPJB) > Configuration > PayPal (under "Payment Methods")
- Next to "Availability" select "Enable this payment method"
- In the PayPal section:
    - Enter your PayPal account email address
    - For "PayPal Environment" choose "Production (Real money)"
- You can set prices for single job postings by going to Settings (WPJB) > Pricing

### To set up Indeed:

- From the WordPress dashboard, go to Settings (WPJB) > Configuration
- Input your Indeed publisher ID in the "Indeed Publisher API Key" field
- Click update

### To import jobs from Indeed:
- From the WordPress dashboard, go to Settings (WPJB) > Import
- Click "Schedule New Import"
- Values for new import as configured by INN:
    - Set "Import from" to "Indeed"
    - "Keyword" field can be anything. On initial set up, I used the labels for the default job categories. For example, "Advocacy and Community Organization"
    - Set the "Import Category" field to the appropriate value
    - "Job Country" set to "United States"
    - "Job Location" set to  "New York" -- note: this field can not be left blank
    - "Posted within" - 7 days
    - "Add jobs" - 10

## Event Listings

### Notes

- Gravity Forms, Events Calendar and Events Calendar Pro plugins should be enabled for event listings
- Since we're using Gravity Forms to feed data to the Events Calendar plugin, the "Event calendar submission form" should not be modified
- If any form fields are changed, there is a check in place that will correct those changes so that the connection between Gravity Forms and Events Calendar will not be broken
- You can modify the price to list an event by editing the "Event listing" field of the "Event calendar submission form" — the very last field on the form edit page
- Accepting payments requires the "PayPal Add-On" for Gravity Forms

### Set up PayPal to accept payments for event listings:

- From the WordPress dashboard, go to Forms > PayPal
- If there is NOT an existing entry for "Event calendar submission form" click "Add New"
- Enter your PayPal email address, set the "Mode" to "Production," set "Transaction Type" to "Products and Services"
- The rest of the configuration form will appear below the "Transaction Type" field
- For "Gravity Form" choose "Event calendar submission form"
- You can safely ignore the rest of the configuration fields

## Registration

- The registration page is a standard WordPress page (titled "Register") with a Largo registration short code embedded in it
- In the event that it is lost or accidentally modified, the original registration short code for CityLimits.org is:

    `[largo_registration_form first_name last_name]`

- The URL for the Register page is "/register." Other components of the CityLimits.org theme depend on this URL, so it should not be altered

## Newsletter sign up

The Constant Contact plugin should be enabled.

- To configure Constant Contact plugin:
    - From the WordPress dashboard, go to Constant Contact > Constant Contact to enter your username and password
    - Go to Constant Contact > Registration & Profile and set:
        - "User Subscription Method": "List Selection"
        - "List Selection Format": "Checkboxes"
        - "Active Contact Lists": choose which lists to make available for users to subscribe to
        - "Signup Title": "Newsletter preferences"

## Donations

- To enable the donate link on your site
- From the WordPress dashboard: Appearance > Theme Options > Basic Settings
- Check the box next to "Show a button in the top header to link to your donation page or form"
- You'll need the link to your donate page, which you can get by creating a donate button via PayPal
