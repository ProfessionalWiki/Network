module.DataSource = ( function () {

	let DataSource = function() {
	};

	DataSource.prototype.getGraph = function() {
		return {
			"nodes": [
				{ "id": "foo", "group": 1 },
				{ "id": "bar", "group": 1 },
				{ "id": "baz", "group": 1 },
				{ "id": "pew1", "group": 2 },
				{ "id": "pew2", "group": 2 },
			],
			"links": [
				{ "source": "foo", "target": "bar" },
				{ "source": "bar", "target": "baz" },
				{ "source": "bar", "target": "pew1" },
				{ "source": "pew2", "target": "pew1" },
			]
		};
	};

	return DataSource;

}() );
