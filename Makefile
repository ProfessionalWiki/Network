.PHONY: ci cs

cs:
	./vendor/bin/psalm

ci: cs
