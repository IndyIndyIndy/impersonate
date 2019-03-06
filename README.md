# TYPO3 Extension "impersonate"
Impersonate frontend users from inside the TYPO3 Backend.

[![Code Climate](https://codeclimate.com/github/IndyIndyIndy/impersonate.svg)](https://codeclimate.com/github/IndyIndyIndy/impersonate)
[![Latest Stable Version](https://poser.pugx.org/christianessl/impersonate/v/stable)](https://packagist.org/packages/christianessl/impersonate)
[![Total Downloads](https://poser.pugx.org/christianessl/impersonate/downloads)](https://packagist.org/packages/christianessl/impersonate)
[![Latest Unstable Version](https://poser.pugx.org/christianessl/impersonate/v/unstable)](https://packagist.org/packages/christianessl/impersonate)
[![License](https://poser.pugx.org/christianessl/impersonate/license)](https://packagist.org/packages/christianessl/impersonate)

## What does it do?

This extension gives backend users with administrator privileges the possibility to authenticate as any specific 
frontend user in the frontend with just a single click from inside the backend.

![Screenshot](/Resources/Public/Screenshots/impersonate.png)

## Requirements

Currently supports TYPO3 8.7 and 9.5 LTS.

## 1. Installation

### Installation with composer

`composer require christianessl/impersonate`. 

### Installation with TER

Open the TYPO3 Extension Manager, search for `impersonate` and install the extension.

## 2. Configuration

- Go to the template module in the backend and include the `Impersonate` template to your main TypoScript template.
- Now open the Constant Editor, choose `module.tx_impersonate` and set the id of the 
target page to redirect an admin to when logging in a feuser via the backend:
    - `module.tx_impersonate.settings.loginRedirectPid = #uid of your target page`

## 3. Usage

- Go to the list module as a backend user with administrator privileges, open a page / sysfolder with frontend user 
records and click the "Impersonate user" button.
- Voila! You are now logged in as the chosen frontend user.