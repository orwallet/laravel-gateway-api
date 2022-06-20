# Gateway SDK for Laravel

Lightweight SDK for Gateway API for Laravel Projects

## Installation

Install the package using composer

```bash
composer require btph/gateway-sdk
```

⚠️ Make sure to publish the Service Provider by running `php artisan vendor:publish` and select the package.

### Environment Variables

After publishing set the following environment variables to your projects `.env` file.

```bash
GATEWAY_API_KEY=<YOUR GATEWAY API KEY>
GATEWAY_SECRET_KEY=<YOUR GATEWAY SECRET KEY>
GATEWAY_API_URL=<GATEWAY API URL>
```

## How to use

## Attaching a customer

To attach a customer details before creating an intent. ⚠️**YOU MUST ALWAYS CALL FIRST** the `attachCustomer` function.

```
// use Btph\GatewaySdk\Facade\Gateway
Gateway::attachCustomer([...customer_object]);
```

### Customer object

| Name             | Type     | Description                                       | Required? |
| :--------------- | :------- | :------------------------------------------------ | :-------- |
| `address_line_1` | `string` | primary address of the customer                   | ✅        |
| `address_line_2` | `string` | secondary address of the customer                 | ❌        |
| `city`           | `string` | residential city of the customer                  | ✅        |
| `country`        | `string` | country of residence of the customer              | ✅        |
| `email`          | `string` | email of the customer                             | ✅        |
| `first_name`     | `string` | first name of the customer                        | ✅        |
| `last_name`      | `string` | last name of the customer                         | ✅        |
| `mobile`         | `string` | mobile number for the customer e.g (+639123456..) | ✅        |
| `state`          | `string` | state of residence of the customer                | ✅        |
| `zip`            | `string` | zip code of the primary address of the customer   | ✅        |

## Creating intents

To create a deposit intent you must first attach a customer by calling `attachCustomer()` function and then call `createDepositIntent()` or `createWithdrawalIntent()` functions.

### Deposit intent

```
// use Btph\GatewaySdk\Facade\Gateway
Gateway::attachCustomer([...customer_object])->createDepositIntent([..intent_details]);
```

### Withdrawal intent

```
// use Btph\GatewaySdk\Facade\Gateway
Gateway::attachCustomer([...customer_object])->createWithdrawalIntent([..intent_details]);
```

### Deposit Intent object

| Name                      | Type     | Description                                                                                   | Required?                                     |
| :------------------------ | :------- | :-------------------------------------------------------------------------------------------- | :-------------------------------------------- |
| `reference_no`            | `string` | reference number from the merchant                                                            | ✅                                            |
| `method`                  | `string` | method that will be used (`credit_debit_card`, `local_bank_transfer`, `third_party_solution`) | ✅                                            |
| `currency`                | `string` | currency of the amount that will be deposited                                                 | ✅                                            |
| `amount`                  | `float`  | amount to be deposited                                                                        | ✅                                            |
| `redirect_url`            | `string` | redirect url after the transaction has been completed                                         | ✅                                            |
| `merchant_account_number` | `string` | the merchants account number from OWL                                                         | ✅ if third party solution is Oriental Wallet |
| `mid`                     | `string` | the merchant's mid                                                                            | ✅ if third party solution is Oriental Wallet |
| `merchant_email`          | `string` | the merchants email from OWL                                                                  | ✅ if third party solution is Oriental Wallet |
| `note`                    | `string` | description of the transactions                                                               | ❌ if third party solution is Oriental Wallet |

### Withdrawal Intent object

| Name             | Type     | Description                                                              | Required? |
| :--------------- | :------- | :----------------------------------------------------------------------- | :-------- |
| `reference_no`   | `string` | reference number from the merchant                                       | ✅        |
| `method`         | `string` | method that will be used (`local_bank_transfer`, `third_party_solution`) | ✅        |
| `debit_currency` | `string` | currency of the debited amount                                           | ✅        |
| `currency`       | `string` | currency of the amount that will be withdrawn                            | ✅        |
| `amount`         | `float`  | amount to be withdrawn                                                   | ✅        |

## Processing withdraw transactions

After creating a **Withdrawal intent** you may now process by calling the `processWithdrawalIntent()` you will need the **Transaction Number**, of the transaction you wish to process.
so it is adviced to store it somewhere inside your application.

### Processing withdrawals

```
// use Btph\GatewaySdk\Facade\Gateway
Gateway::attachCustomer([...customer_object])->processWithdrawalIntent(<transaction number>, [..details]);
```

the first argument of `processWithdrawalIntent()` is the transaction number you wish to process, and the second argument will depend on what transaction method you use when creating the intent.

### Processing Local Bank Transfers

To process withdrawals using local bank transfer, the following key pair values are required

| Name               | Type     | Description                 | Required? |
| :----------------- | :------- | :-------------------------- | :-------- |
| `account_name`     | `string` | the customer account name   | ✅        |
| `account_number`   | `string` | the customer account number | ✅        |
| `bank_name`        | `string` | the bank name               | ✅        |
| `bank_branch_name` | `string` | the branch name of the bank | ✅        |
| `bank_city`        | `string` | the bank city of the bank   | ✅        |
| `bank_code`        | `string` | the bank code of the bank   | ✅        |
| `bank_province`    | `string` | bank province of the bank   | ✅        |

#### ⚠️ An important note when processing withdrawals using JPY currencies as the credit currency

To process this kind of transactions an additional key pair values are required.
| Name | Type | Description | Required? |
| :-------- | :------- | :------- | :-------- |
| `account_name_katakana` | `string` | the customers account name in katakana | ✅ |
| `account_name_kanji` | `string` | the customers account name in kanji | ✅ |

### Processing third party solutions

#### Oriental Wallet

For Oriental Wallet withdrawals the following key pair values are required.

| Name                          | Type     | Description                     | Required? |
| :---------------------------- | :------- | :------------------------------ | :-------- |
| `receiver_individual_account` | `string` | the receiver individual account | ✅        |
| `merchant_account_number`     | `string` | the merchants account number    | ✅        |
| `mid`                         | `string` | the merchant's mid              | ✅        |
| `merchant_email`              | `string` | the merchants email             | ✅        |
