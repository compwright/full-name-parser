lint:
	vendor/bin/phpstan analyse --level 8 --memory-limit 1G ./src ./tests

fix:
	vendor/bin/php-cs-fixer fix

test:
	vendor/bin/phpunit --display-skipped --display-incomplete --display-deprecations --display-phpunit-deprecations ./tests

ci: lint test
