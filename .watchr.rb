watch('(.*).php') { |m| code_changed(m[0]) }
watch('./Tests/(.*).php') { |m| code_changed(m[0]) }

def code_changed(file)
    system( "phpunit --colors Tests/" )
end

