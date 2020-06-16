module.stub.Cats = (function() {
	return {
		backLinks: [
			{
				"batchcomplete": "",
				"limits": {
					"backlinks": 5000
				},
				"query": {
					"backlinks": [
						{
							"pageid": 1,
							"ns": 0,
							"title": "Main Page"
						}
					]
				}
			},
			{
				"readyState": 4,
				"responseText": "{\"batchcomplete\":\"\",\"limits\":{\"backlinks\":5000},\"query\":{\"backlinks\":[{\"pageid\":1,\"ns\":0,\"title\":\"Main Page\"}]}}",
				"responseJSON": {
					"batchcomplete": "",
					"limits": {
						"backlinks": 5000
					},
					"query": {
						"backlinks": [
							{
								"pageid": 1,
								"ns": 0,
								"title": "Main Page"
							}
						]
					}
				},
				"status": 200,
				"statusText": "OK"
			}
		],

		outgoingLinks: [
			{
				"batchcomplete": "",
				"query": {
					"pages": {
						"1521": {
							"pageid": 1521,
							"ns": 0,
							"title": "Cats",
							"links": [
								{
									"ns": 0,
									"title": "Kittens"
								},
								{
									"ns": 0,
									"title": "Looong Cat"
								},
								{
									"ns": 0,
									"title": "Main Page"
								},
								{
									"ns": 0,
									"title": "Nyan Cat"
								}
							]
						}
					}
				},
				"limits": {
					"links": 5000
				}
			},
			{
				"readyState": 4,
				"responseText": "{\"batchcomplete\":\"\",\"query\":{\"pages\":{\"1521\":{\"pageid\":1521,\"ns\":0,\"title\":\"Cats\",\"links\":[{\"ns\":0,\"title\":\"Kittens\"},{\"ns\":0,\"title\":\"Looong Cat\"},{\"ns\":0,\"title\":\"Main Page\"},{\"ns\":0,\"title\":\"Nyan Cat\"}]}}},\"limits\":{\"links\":5000}}",
				"responseJSON": {
					"batchcomplete": "",
					"query": {
						"pages": {
							"1521": {
								"pageid": 1521,
								"ns": 0,
								"title": "Cats",
								"links": [
									{
										"ns": 0,
										"title": "Kittens"
									},
									{
										"ns": 0,
										"title": "Looong Cat"
									},
									{
										"ns": 0,
										"title": "Main Page"
									},
									{
										"ns": 0,
										"title": "Nyan Cat"
									}
								]
							}
						}
					},
					"limits": {
						"links": 5000
					}
				},
				"status": 200,
				"statusText": "OK"
			}
		]
	};
}) ();
