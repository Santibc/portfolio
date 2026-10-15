<x-input label="Nombre de la categoría" name="titulo" :value="old('titulo', $elemento->titulo)" required maxlength="60"
         placeholder="Pan artesanal" hint="También se usa en el mensaje de WhatsApp: «Quisiera pedir: …»." />

<x-textarea label="Descripción" name="descripcion" rows="3" :value="old('descripcion', $elemento->descripcion)" required maxlength="200"
            placeholder="Masas de fermentación lenta, corteza crujiente y miga suave." />

<input type="hidden" name="activo" value="0">
<x-toggle name="activo" label="Visible en la página" description="Apágalo para ocultarlo sin borrarlo."
          :checked="(bool) old('activo', $elemento->activo ?? true)" />
