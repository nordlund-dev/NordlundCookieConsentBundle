module.exports = (property) => `
    public function ${property.setterName()}(${property.getTypeHint() ? property.getTypeHint() + ' ' : '' }\$${property.getName()}): self
    {
        $this->${property.getName()} = \$${property.getName()};

        return $this;
    }
`