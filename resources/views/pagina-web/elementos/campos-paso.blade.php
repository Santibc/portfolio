<x-input label="Título del paso" name="titulo" :value="old('titulo', $elemento->titulo)" required maxlength="60"
         placeholder="Amasado a mano" />

<x-textarea label="Texto" name="texto" rows="4" :value="old('texto', $elemento->texto)" required maxlength="300" />
