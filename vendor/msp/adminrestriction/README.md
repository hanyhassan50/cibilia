# MSP AdminRestriction

Deny access to Magento backend from **unauthorized IPs**.

This module represents one of the **most effective Magento backend protection policy**.

> Member of **MSP Security Suite**
>
> See: https://github.com/magespecialist/m2-MSP_Security_Suite

Did you lock yourself out from Magento backend? <a href="https://github.com/magespecialist/m2-MSP_AdminRestriction#emergency-commandline-disable">click here.</a>

## Installing on Magento2:

**1. Install using composer**

From command line: 

`composer require msp/adminrestriction`<br />
`php bin/magento setup:upgrade`

**2. Enable and configure from your Magento backend config**

<img src="https://raw.githubusercontent.com/magespecialist/m2-MSP_AdminRestriction/master/screenshots/config.png" />

## Emergency commandline disable:

If you messed up with IP list, you can disable or change authorized ip list from command-line:

**Disable filter:**

`php bin/magento msp:security:admin_restriction:ip disable`

**Authorize a new IP list:**

`php bin/magento msp:security:admin_restriction:ip 127.0.0.1,192.168.0.0/24`
