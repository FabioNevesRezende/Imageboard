<!DOCTYPE html>
<html lang="pt">
    
@include('partials._head') <!-- cabeçalho -->
<body>

        @include('partials._nav')
        
        @include('partials._msg')
        
        @yield('conteudo')
        
        
        @include('partials._footer')
        @include('partials._jsincludes')
        
        @yield('scripts')
        
</body>
    
</html>