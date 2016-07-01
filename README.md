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

Parse log file and load to database:

``` php
cat examples/probe-requests.log | php educhack.php
```


## License

This project is under [MIT License](LICENSE).
