module.exports = (property) => `
    public function ${property.getterName()}(): ${property.getType() ? property.getType() : 'mixed'}
    {
        return $this->${property.getName()};
    }
`