.PHONY: ci cs

cs:
	./vendor/bin/psalm
	./vendor/bin/phpstan analyse -c phpstan.neon --no-progress

ci: cs
