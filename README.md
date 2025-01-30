# TYPO3 Extension "impersonate"

> Impersonate frontend users from inside the TYPO3 Backend.

[![Code Climate](https://codeclimate.com/github/IndyIndyIndy/impersonate.svg)](https://codeclimate.com/github/IndyIndyIndy/impersonate)
[![TYPO3 13](https://img.shields.io/badge/TYPO3-13-orange.svg)](https://get.typo3.org/version/13)
[![Latest Stable Version](https://poser.pugx.org/christianessl/impersonate/v/stable)](https://packagist.org/packages/christianessl/impersonate)
[![Total Downloads](https://poser.pugx.org/christianessl/impersonate/downloads)](https://packagist.org/packages/christianessl/impersonate)
[![Monthly Downloads](https://poser.pugx.org/christianessl/impersonate/d/monthly)](https://packagist.org/packages/christianessl/impersonate)
[![Latest Unstable Version](https://poser.pugx.org/christianessl/impersonate/v/unstable)](https://packagist.org/packages/christianessl/impersonate)
[![License](https://poser.pugx.org/christianessl/impersonate/license)](https://packagist.org/packages/christianessl/impersonate)

## What does it do?

This extension gives backend users with administrator privileges the possibility to authenticate as any specific
frontend user in the frontend with just a single click from inside the backend. This **does not include** default
backend users.

And remember: *With great power comes great responsibility*. The purpose of this extension is mainly to allow for a tech
support login as a specific user and see potential problems and bugs from the perspective of the user as well as doing
tech support actions while impersonating the specified user account.

![Screenshot](/Resources/Public/Screenshots/impersonate.png)

## Compatibility

| Impersonate | TYPO3     | PHP       | Support / Development                |
|-------------|-----------|-----------|--------------------------------------|
| 4.x         | 13.4      | 8.2 - 8.4 | features, bugfixes, security updates |
| 3.x         | 12.4      | 8.1 - 8.3 | features, bugfixes, security updates |
| 2.x         | 11.5      | 7.4 - 8.3 | bugfixes, security updates           |
| 1.1.x       | 10.4      | 7.0 - 7.4 | none                                 |
| 1.0.x       | 8.7 - 9.5 | 7.0 - 7.4 | none                                 |

---

## 1. Installation

### Installation with composer

`composer require christianessl/impersonate`.

### Installation with TER

Open the TYPO3 Extension Manager, search for `impersonate` and install the extension.

## 2. Configuration

- Go to the template module in the backend and include the `Impersonate` template to your main TypoScript template.
- Now open the Constant Editor, choose `module.tx_impersonate` and set the id of the target page to redirect an admin to
  when logging in a feuser via the backend:
    - `module.tx_impersonate.settings.loginRedirectPid = #uid of your target page`

## 3. Usage

- Go to the list module as a backend user with administrator privileges, open a page / sysfolder with frontend user
  records and click the "Impersonate user" button.
- Voila! You are now logged in as the chosen frontend user.

---

## Authors

* See the list of [contributors](https://github.com/IndyIndyIndy/impersonate/graphs/contributors) who participated in this project.
