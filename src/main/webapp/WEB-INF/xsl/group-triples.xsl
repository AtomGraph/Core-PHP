<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns="http://www.w3.org/1999/xhtml"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
exclude-result-prefixes="xsl rdf">

    <xsl:key name="resources" match="*[@rdf:about] | *[@rdf:nodeID]" use="@rdf:about | @rdf:nodeID"/>

    <xsl:template match="rdf:RDF">
        <xsl:copy>
            <!-- URI resources -->
            <xsl:for-each select="*[@rdf:about][count(. | key('resources', @rdf:about)[1]) = 1]">
                <xsl:copy>
                    <xsl:copy-of select="@*"/>
                    <xsl:for-each select="key('resources', @rdf:about)">
                        <xsl:copy-of select="*"/>
                    </xsl:for-each>
                </xsl:copy>
            </xsl:for-each>

            <!-- blank nodes -->
            <xsl:for-each select="*[@rdf:nodeID][count(. | key('resources', @rdf:nodeID)[1]) = 1]">
                <xsl:copy>
                    <xsl:copy-of select="@*"/>
                    <xsl:for-each select="key('resources', @rdf:nodeID)">
                        <xsl:copy-of select="*"/>
                    </xsl:for-each>
                </xsl:copy>
            </xsl:for-each>
        </xsl:copy>
    </xsl:template>

</xsl:stylesheet>
