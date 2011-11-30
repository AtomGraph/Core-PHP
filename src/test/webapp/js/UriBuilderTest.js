UriBuilderTest = TestCase("UriBuilder");

UriBuilderTest.prototype.testBuild = function() {
    var builder = UriBuilder.fromPath("/aaa/bbb/ccc_{year}{month}{day}.jpg");
    assertEquals("/aaa/bbb/ccc_20110825.jpg", builder.build(2011, "08", "25"));

    builder = UriBuilder.fromPath("/striben/{year: \d{4}}/{month: \d{2}}/{day: \d{2}}")
            .host("localhost");
    assertEquals("http://localhost/striben/2011/08/25", builder.build(2011, "08", "25"));

    builder = UriBuilder.fromPath("aaa/bbb/ccc.jpg");
    assertEquals("aaa/bbb/ccc.jpg", builder.build());
}

