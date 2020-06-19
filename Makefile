.PHONY: ci cs

cs:
	./vendor/bin/phpstan analyse -c phpstan.neon --no-progress
	./vendor/bin/psalm

ci: cs
