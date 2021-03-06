<?xml version="1.0" encoding="UTF-8"?>
<!--
Copyright 2011 Graphity Team

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
-->
<xsl:stylesheet version="1.0"
xmlns="http://www.w3.org/1999/xhtml"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
xmlns:php="http://php.net/xsl"
exclude-result-prefixes="xsl rdf">

    <!-- converts RDF/XML to JSON-LD -->

	<xsl:output indent="no" omit-xml-declaration="yes" method="text" encoding="UTF-8" media-type="application/json"/>
	<xsl:strip-space elements="*"/>

    <xsl:template match="rdf:RDF">
        <xsl:text>[</xsl:text>
            <xsl:apply-templates/>
        <xsl:text>]</xsl:text>
    </xsl:template>

    <!-- subject -->
    <xsl:template match="*[@rdf:about or @rdf:nodeID]">
        <xsl:text>{</xsl:text>
        <xsl:if test="@rdf:about">
            <xsl:text>"@subject": "</xsl:text>
            <xsl:value-of select="@rdf:about"/>
            <xsl:text>", </xsl:text>
        </xsl:if>
        <xsl:apply-templates/>
        <xsl:text>}</xsl:text>
        <xsl:if test="position() != last()">, </xsl:if>
    </xsl:template>

    <!-- property -->
    <xsl:template match="*[@rdf:about or @rdf:nodeID]/*">
        <xsl:text>"</xsl:text>
        <xsl:value-of select="concat(namespace-uri(.), local-name(.))"/>
        <xsl:text>":</xsl:text>
        <xsl:choose>
            <xsl:when test="@rdf:resource">
                <xsl:text>{</xsl:text>
                <xsl:apply-templates select="@*"/>
                <xsl:text>}</xsl:text>
            </xsl:when>
            <xsl:when test="@rdf:datatype or @xml:lang">
                <xsl:text>{</xsl:text>
                <xsl:apply-templates select="@*"/>
                <xsl:text>, </xsl:text>
                <xsl:apply-templates select="text()"/>
                <xsl:text>}</xsl:text>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates select="* | text()"/>
            </xsl:otherwise>
        </xsl:choose>
        <xsl:if test="position() != last()">, </xsl:if>
    </xsl:template>

    <!-- property rdf:type -->
    <xsl:template match="*[@rdf:about or @rdf:nodeID]/rdf:type">
        <xsl:text>"@type": "</xsl:text>
        <xsl:value-of select="@rdf:resource"/>
        <xsl:text>"</xsl:text>
        <xsl:if test="position() != last()">, </xsl:if>
    </xsl:template>

    <!-- object resource -->
    <xsl:template match="*[@rdf:about or @rdf:nodeID]/*/@rdf:resource">
        <xsl:text>"@iri": "</xsl:text>
        <xsl:value-of select="."/>
        <xsl:text>"</xsl:text>
    </xsl:template>

    <!-- object literal datatype -->
    <xsl:template match="*[@rdf:about or @rdf:nodeID]/*/@rdf:datatype">
        <xsl:text>"@datatype": "</xsl:text>
        <xsl:value-of select="."/>
        <xsl:text>"</xsl:text>
        <xsl:if test="position() != last()">, </xsl:if>
    </xsl:template>

    <!-- object literal language -->
    <xsl:template match="*[@rdf:about or @rdf:nodeID]/*/@xml:lang">
        <xsl:text>"@language": "</xsl:text>
        <xsl:value-of select="."/>
        <xsl:text>"</xsl:text>
        <xsl:if test="position() != last()">, </xsl:if>
    </xsl:template>

    <!-- object literal (plain) -->
    <xsl:template match="*[@rdf:about or @rdf:nodeID]/*/text()">
        <xsl:text>"</xsl:text>
        <xsl:value-of select="."/>
        <xsl:text>"</xsl:text>
    </xsl:template>

    <!-- object literal (w/ datatype or language) -->
    <xsl:template match="*[@rdf:about or @rdf:nodeID]/*[@rdf:datatype or @xml:lang]/text()">
        <xsl:text>"@literal": "</xsl:text>
        <xsl:value-of select="."/>
        <xsl:text>"</xsl:text>
    </xsl:template>

    <!-- object XMLLiteral -->
    <!--
    <xsl:template match="*[@rdf:about or @rdf:nodeID]/*[@rdf:datatype = '&rdf;XMLLiteral']/*">
        <xsl:text>"@literal": "</xsl:text>
        <xsl:value-of select="."/>
        <xsl:text>"</xsl:text>
    </xsl:template>
    -->

    <xsl:template match="node()"/>

    <xsl:template match="@* | node()" mode="json-identity">
        <xsl:copy>
            <xsl:apply-templates select="@* | node()" mode="json-identity"/>
        </xsl:copy>
    </xsl:template>

    <xsl:template match="text()" mode="json-identity">
        <!-- <xsl:value-of select="php:function('json_encode', string(.))"/> -->
        <!-- for some reason json_encode() adds extra quotes which need to be removed -->
        <xsl:value-of select="php:function('trim', php:function('json_encode', string(.)), '&quot;')"/>
    </xsl:template>

</xsl:stylesheet>