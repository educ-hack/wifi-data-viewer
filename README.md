# WiFi data viewer

Parse exposed wifi data from Raspberries and display them in an ihm.


## Installation

``` bash
git clone git@github.com:educ-hack/wifi-data-viewer.git
cd wifi-data-viewer

composer update
php bin\console orm:generate:entities src
php bin\console orm:schema-tool:create
```


## Usage

This project provides an Api to post wifi logs:

```
POST api/prob

[
    {
        "mac": "ef:c0:e5:f4:23:69",
        "sniffer_id": "toto",
        "time": "12:45:50",
        "noise": 65,
        "requested_ssid": "CTP Corporate"
    }
]
```


Then go to the user interface.


## License

This project is under [MIT License](LICENSE).
