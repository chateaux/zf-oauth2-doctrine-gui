# zf-oauth2-doctrine-gui
A GUI for managing zfcampus/zf-oauth2-doctrine

## Installation

Install using composer: "api-skeletons/zf-oauth2-doctrine-gui": "dev-master"

Add the following to: 'DoctrineGui' to your application.config.php file

## Pre-Requisites

You will require:

1. zf-oauth2-doctrine
2. Asset Manager

## How it works

Once installed, visit: /zf-oauth2-doctrine-gui/overview to get started. If you use ACL, make sure the route is added to your ACL controller.


## Setting up a client

You may wish to setup a client:

1. For SSO Access
2. To facilitate secure server to server communication (JWT)

An SSO client would require:

1. An implicit grant
2. A basic scope (create it if you do not have it)
3. A re-direct URI (the expected return address)
4. And a password

Once you have created a client with an implicit grant, click on the test button and you will be directed to the URI with an access token in the URL.

A JWT based client would require:
1. A urn:ietf:params:oauth:grant-type:jwt-bearer grant
2. A basic scope (or other as you require in your Api)
3. You will still need to add a password and a return uri

Once you have created a client with the urn:ietf:params:oauth:grant-type:jwt-bearer grant, you will then need to add a public key. We have provided a test private/public key pair in the utils folder. Copy the public key then click on the "add public key" button and paste it into the space provided. You will also need to add a user_id (subject), this will link the client to a user in your system (it is up to you to use this ID with your API or not, it will not effect its operation in any manner).

Now that your public key has been added, click on the test button and you will be taken to a form where you will need to enter a private key (utils folder). Enter the key and press "create JWT". The form will be returned with a code that looks something like this:

eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJ0ZXN0X2NsaWVudDMiLCJzdWIiOiIxIiwiYXVkIjoiIiwiZXhwIjoxNDQzNjkwODgyLCJpYXQiOjE0NDM2OTA1ODJ9.jaqjrNsz5a9Si0mWmYdhuRIBHbj1gGSSaTBqJvSZCsC_Yg7DTexzIF6vAvI4d5BD-qEnANYDN2b5SBbwho2Borm-s5Xzefsqpy_emURaj70hnuZjwf8cpfq39krXy1g0ekK21CiOP5CPo0MFgY8Q3wn70CoWY42EmTDFgRfVrwVPdG_l2ZRc6LIACztu4z6f4cuwYR3d5WPzXgyS5fBtplON7o9bBoxODR4mo9diUkeHGLnl21MjK8HWbthiErnjpnB4lsFcwFWMgFigd4UVYrULZ_P2NZkHtf6Xvhu2BlwUozbXvtoaTKKdlHf5dfepq0hM37LbK6WQfYmVL3vwlg

