module.StubPageConnectionRepo = ( function () {

	let StubPageConnectionRepo = function() {
	};

	StubPageConnectionRepo.prototype.getConnections = function() {
		let deferred = $.Deferred();

		deferred.resolve({
			"pages": [
				{ "title": "foo", "ns": 1 },
				{ "title": "bar", "ns": 1 },
				{ "title": "baz", "ns": 1 },
				{ "title": "pew1", "ns": 2 },
				{ "title": "pew2", "ns": 2 },
			],
			"links": [
				{ "source": "foo", "target": "bar" },
				{ "source": "bar", "target": "baz" },
				{ "source": "bar", "target": "pew1" },
				{ "source": "pew2", "target": "pew1" },
			]
		});

		return deferred;
	};

	return StubPageConnectionRepo;

}() );
