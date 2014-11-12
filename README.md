PureCaptcha
===========

OWASP PureCaptcha project aims to help developers use easy-to-user independent CAPTCHAs in their project with zero hassle.
Traditionally, using CAPTCHAs required making accounts in a third party provider and using rigorous APIs, or installing many libraries
and then using text rendering and image modification libraries to render a CAPTCHA image.

PureCaptcha on the other hand, aims to do all of the above in one step. It includes very simple code to render a few ASCII characters
into bitmap images, and code to modify, scale, rotate and distort simple bitmap images. It then outputs the CAPTCHA as a simple 
monochrome BMP image (which has a very small size).

It also provides example usages for CAPTCHAs. It is of utmost importance to properly use CAPTCHAs, otherwise side-channel and logical
attacks would be viable on the system. 

Read more about OWASP PureCaptcha at https://www.owasp.org/index.php/OWASP_PureCaptcha 
