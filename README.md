# WiFi data viewer

Parse exposed wifi data from Raspberries and display them in an ihm.


## Installation

``` bash
git clone git@github.com:educ-hack/wifi-data-viewer.git
cd wifi-data-viewer

composer update
vendor\bin\doctrine orm:generate-entities .
vendor\bin\doctrine orm:schema-tool:create
```


## Usage

Parse log file and load to database:

``` php
cat examples/probe-requests.log | php educhack.php
```


## License

This project is under [MIT License](LICENSE).
