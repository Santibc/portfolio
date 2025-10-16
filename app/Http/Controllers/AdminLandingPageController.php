<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LandingConfiguracion;
use App\Models\LandingContactInfo;
use App\Models\LandingAbout;
use App\Models\LandingLayoutConfig;
use App\Models\Page;
use App\Models\Seo;
use App\Models\LandingPricingConfig;
use App\Models\LandingPricingRange;
use App\Models\LandingHeroSection;
use App\Models\LandingHeroValue;
use App\Models\LandingSocialMedia;
use App\Models\LandingHomeAbout;
use App\Models\LandingHomeServices;
use App\Models\LandingServiceItem;
use App\Models\LandingServiceImage;
use App\Models\LandingTestimonial;
use App\Models\LandingAboutValue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Mail\ContactFormMail;

class AdminLandingPageController extends Controller
{
    public function index()
    {
        $config = LandingConfiguracion::first();
        $contactInfo = LandingContactInfo::first();
        $about = LandingAbout::first();
        $layoutConfig = LandingLayoutConfig::first();
        $pricingConfig = LandingPricingConfig::first();
        $pricingRanges = LandingPricingRange::orderBy('order')->get();

        // New Landing Content
        $heroSection = LandingHeroSection::first();
        $heroValues = LandingHeroValue::orderBy('order')->get();
        $socialMedia = LandingSocialMedia::first();
        $homeAbout = LandingHomeAbout::first();
        $homeServices = LandingHomeServices::first();
        $serviceItems = LandingServiceItem::orderBy('order')->get();
        $serviceImages = LandingServiceImage::orderBy('order')->get();
        $testimonials = LandingTestimonial::orderBy('order')->get();
        $aboutValues = LandingAboutValue::orderBy('order')->get();

        $this->ensureLandingPagesExist();
        $pages = Page::where('page_type', 'landing')->get();
        $seoConfigs = Seo::with('page')->get();

        return view('admin.landing.index', compact(
            'config', 'contactInfo', 'about', 'layoutConfig', 'pages', 'seoConfigs',
            'pricingConfig', 'pricingRanges',
            'heroSection', 'heroValues', 'socialMedia', 'homeAbout', 'homeServices',
            'serviceItems', 'serviceImages', 'testimonials', 'aboutValues'
        ));
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_description' => 'required|string',
            'contact_email' => 'nullable|email',
            'services_button_url' => 'nullable|string'
        ]);

        $config = LandingConfiguracion::first();

        if ($config) {
            $config->update($request->all());
        } else {
            LandingConfiguracion::create($request->all());
        }

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }

    public function updateContactInfo(Request $request)
    {
        $request->validate([
            'contact_hero_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email',
            'receive_messages_email' => 'required|email',
            'google_maps_embed' => 'nullable|string'
        ]);

        $contactInfo = LandingContactInfo::first();

        if ($contactInfo) {
            $contactInfo->update($request->all());
        } else {
            LandingContactInfo::create($request->all());
        }

        return redirect()->back()->with('success', 'Información de contacto actualizada correctamente.');
    }

    public function sendContactEmail(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        $contactInfo = LandingContactInfo::first();

        if ($contactInfo && $contactInfo->receive_messages_email) {
            try {
                Mail::to($contactInfo->receive_messages_email)->send(
                    new ContactFormMail($request->all())
                );

                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error' => 'Error al enviar el mensaje']);
            }
        }

        return response()->json(['success' => false, 'error' => 'No se pudo enviar el mensaje']);
    }

    // Métodos para página Nosotros
    public function updateAbout(Request $request)
    {
        $request->validate([
            'page_title' => 'required|string|max:255',
            'purpose_title' => 'required|string|max:255',
            'purpose_content' => 'required|string',
            'mission_title' => 'required|string|max:255',
            'mission_content' => 'required|string',
            'vision_title' => 'required|string|max:255',
            'vision_content' => 'required|string',
            'years_experience' => 'required|integer|min:0',
            'happy_clients' => 'required|integer|min:0',
            'client_satisfaction' => 'required|integer|min:0|max:100',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $about = LandingAbout::first();
        $data = $request->except('main_image');

        // Manejar subida de imagen
        if ($request->hasFile('main_image')) {
            // Eliminar imagen anterior si existe
            if ($about && $about->main_image_path) {
                $oldImagePath = public_path($about->main_image_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Crear directorio si no existe
            $uploadPath = public_path('images/about');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generar nombre único para la imagen
            $fileName = time() . '_' . uniqid() . '.' . $request->file('main_image')->getClientOriginalExtension();

            // Mover la imagen al directorio público
            $request->file('main_image')->move($uploadPath, $fileName);

            $data['main_image_path'] = 'images/about/' . $fileName;
        }

        if ($about) {
            $about->update($data);
        } else {
            LandingAbout::create($data);
        }

        return redirect()->back()->with('success', 'Página Nosotros actualizada correctamente.');
    }

    // Métodos para configuración del layout
    public function updateLayoutConfig(Request $request)
    {
        $request->validate([
            'site_title' => 'required|string|max:255',
            'footer_description' => 'nullable|string|max:500',
            'topbar_email' => 'required|email',
            'topbar_phone' => 'required|string|max:255',
            'twitter_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'footer_address' => 'required|string|max:255',
            'footer_city' => 'required|string|max:255',
            'footer_phone' => 'required|string|max:255',
            'footer_email' => 'required|email',
            'copyright_company' => 'required|string|max:255'
        ]);

        $layoutConfig = LandingLayoutConfig::first();

        if ($layoutConfig) {
            $layoutConfig->update($request->all());
        } else {
            LandingLayoutConfig::create($request->all());
        }

        return redirect()->back()->with('success', 'Configuración del sitio actualizada correctamente.');
    }

    // SEO Methods
    private function ensureLandingPagesExist()
    {
        $landingPages = [
            ['name' => 'Inicio', 'slug' => 'home', 'url_path' => '/'],
            ['name' => 'Nosotros', 'slug' => 'nosotros', 'url_path' => '/nosotros'],
            ['name' => 'Equipo', 'slug' => 'equipo', 'url_path' => '/equipo'],
            ['name' => 'Contacto', 'slug' => 'contacto', 'url_path' => '/contacto'],
        ];

        foreach ($landingPages as $pageData) {
            Page::firstOrCreate(
                ['slug' => $pageData['slug']],
                array_merge($pageData, ['page_type' => 'landing'])
            );
        }
    }

    public function updateSeo(Request $request)
    {
        $request->validate([
            'page_id' => 'required|exists:pages,id',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:500',
            'robots' => ['required', Rule::in(['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow'])],
            'focus_keyword' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        $data = $request->only(['page_id', 'meta_title', 'meta_description', 'meta_keywords', 'canonical_url', 'robots', 'focus_keyword']);
        $data['is_active'] = $request->has('is_active');

        $seo = Seo::where('page_id', $request->page_id)->first();

        if ($seo) {
            $seo->update($data);
        } else {
            Seo::create($data);
        }

        return redirect()->back()->with('success', 'Configuración SEO actualizada correctamente.');
    }

    public function getSeoData($pageId)
    {
        $seo = Seo::where('page_id', $pageId)->first();
        return response()->json($seo);
    }

    public function deleteSeo($id)
    {
        $seo = Seo::findOrFail($id);
        $seo->delete();

        return redirect()->back()->with('success', 'Configuración SEO eliminada correctamente.');
    }

    public function updatePricingConfig(Request $request)
    {
        $request->validate([
            'whatsapp_number' => 'required|string|max:20',
            'extra_heavy_duty' => 'required|numeric|min:0',
            'inside_fridge_ea' => 'required|numeric|min:0',
            'inside_oven_ea' => 'required|numeric|min:0',
            'post_construction_government' => 'required|numeric|min:0',
            'post_construction_private' => 'required|numeric|min:0',
            'window_clean_interior' => 'required|numeric|min:0',
            'window_clean_exterior' => 'required|numeric|min:0',
            'recurring_weekly_discount' => 'required|integer|min:0|max:100',
            'recurring_biweekly_discount' => 'required|integer|min:0|max:100',
        ]);

        $pricingConfig = LandingPricingConfig::first();

        if ($pricingConfig) {
            $pricingConfig->update($request->all());
        } else {
            LandingPricingConfig::create($request->all());
        }

        return redirect()->back()->with('success', 'Configuración de precios actualizada correctamente.');
    }

    public function updatePricingRange(Request $request, $id)
    {
        $request->validate([
            'sq_ft_min' => 'required|integer|min:0',
            'sq_ft_max' => 'required|integer|min:0',
            'initial_clean' => 'required|numeric|min:0',
            'weekly' => 'required|numeric|min:0',
            'biweekly' => 'required|numeric|min:0',
            'monthly' => 'required|numeric|min:0',
            'deep_clean' => 'required|numeric|min:0',
            'move_out_clean' => 'required|numeric|min:0',
        ]);

        $range = LandingPricingRange::findOrFail($id);
        $range->update($request->all());

        return redirect()->back()->with('success', 'Rango de precios actualizado correctamente.');
    }

    // ========================================
    // HERO SECTION METHODS
    // ========================================

    public function updateHeroSection(Request $request)
    {
        $request->validate([
            'subtitle' => 'nullable|string|max:500',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096'
        ]);

        $heroSection = LandingHeroSection::first();
        $data = $request->except('hero_image');

        // Manejar subida de imagen
        if ($request->hasFile('hero_image')) {
            // Eliminar imagen anterior si existe
            if ($heroSection && $heroSection->hero_image_path) {
                $oldImagePath = public_path($heroSection->hero_image_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Crear directorio si no existe
            $uploadPath = public_path('images/hero');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generar nombre único para la imagen
            $fileName = time() . '_' . uniqid() . '.' . $request->file('hero_image')->getClientOriginalExtension();

            // Mover la imagen al directorio público
            $request->file('hero_image')->move($uploadPath, $fileName);

            $data['hero_image_path'] = 'images/hero/' . $fileName;
        }

        if ($heroSection) {
            $heroSection->update($data);
        } else {
            LandingHeroSection::create($data);
        }

        return redirect()->back()->with('success', 'Sección Hero actualizada correctamente.');
    }

    public function storeHeroValue(Request $request)
    {
        $request->validate([
            'icon_class' => 'required|string|max:255',
            'title' => 'required|string|max:255'
        ]);

        $maxOrder = LandingHeroValue::max('order') ?? 0;

        LandingHeroValue::create([
            'icon_class' => $request->icon_class,
            'title' => $request->title,
            'order' => $maxOrder + 1
        ]);

        return redirect()->back()->with('success', 'Valor agregado correctamente.');
    }

    public function updateHeroValue(Request $request, $id)
    {
        $request->validate([
            'icon_class' => 'required|string|max:255',
            'title' => 'required|string|max:255'
        ]);

        $heroValue = LandingHeroValue::findOrFail($id);
        $heroValue->update($request->only(['icon_class', 'title']));

        return redirect()->back()->with('success', 'Valor actualizado correctamente.');
    }

    public function deleteHeroValue($id)
    {
        $heroValue = LandingHeroValue::findOrFail($id);
        $heroValue->delete();

        return redirect()->back()->with('success', 'Valor eliminado correctamente.');
    }

    // ========================================
    // SOCIAL MEDIA METHODS
    // ========================================

    public function updateSocialMedia(Request $request)
    {
        $request->validate([
            'facebook_url' => 'nullable|url|max:500',
            'twitter_url' => 'nullable|url|max:500',
            'instagram_url' => 'nullable|url|max:500',
            'linkedin_url' => 'nullable|url|max:500',
            'youtube_url' => 'nullable|url|max:500',
            'tiktok_url' => 'nullable|url|max:500',
            'whatsapp_number' => 'nullable|string|max:20'
        ]);

        $socialMedia = LandingSocialMedia::first();

        if ($socialMedia) {
            $socialMedia->update($request->all());
        } else {
            LandingSocialMedia::create($request->all());
        }

        return redirect()->back()->with('success', 'Redes sociales actualizadas correctamente.');
    }

    // ========================================
    // HOME ABOUT METHODS
    // ========================================

    public function updateHomeAbout(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'title' => 'required|string|max:255',
            'lead_text' => 'required|string',
            'description' => 'required|string',
            'years_experience' => 'required|integer|min:0',
            'happy_clients' => 'required|integer|min:0',
            'client_satisfaction' => 'required|integer|min:0|max:100',
            'cta_button_text' => 'nullable|string|max:100',
            'cta_button_url' => 'nullable|string|max:255'
        ]);

        $homeAbout = LandingHomeAbout::first();
        $data = $request->except('image');

        // Manejar subida de imagen
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($homeAbout && $homeAbout->image_path) {
                $oldImagePath = public_path($homeAbout->image_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Crear directorio si no existe
            $uploadPath = public_path('images/home_about');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generar nombre único para la imagen
            $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();

            // Mover la imagen al directorio público
            $request->file('image')->move($uploadPath, $fileName);

            $data['image_path'] = 'images/home_about/' . $fileName;
        }

        if ($homeAbout) {
            $homeAbout->update($data);
        } else {
            LandingHomeAbout::create($data);
        }

        return redirect()->back()->with('success', 'Sección Nosotros del home actualizada correctamente.');
    }

    // ========================================
    // HOME SERVICES METHODS
    // ========================================

    public function updateHomeServices(Request $request)
    {
        $request->validate([
            'section_title' => 'nullable|string|max:255',
            'section_description' => 'nullable|string|max:500',
            'commercial_title' => 'nullable|string|max:255',
            'residential_title' => 'nullable|string|max:255',
            'eco_friendly_note' => 'nullable|string'
        ]);

        $homeServices = LandingHomeServices::first();

        if ($homeServices) {
            $homeServices->update($request->all());
        } else {
            LandingHomeServices::create($request->all());
        }

        return redirect()->back()->with('success', 'Sección Servicios del home actualizada correctamente.');
    }

    public function storeServiceItem(Request $request)
    {
        $request->validate([
            'type' => 'required|in:commercial,residential',
            'icon_class' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $maxOrder = LandingServiceItem::max('order') ?? 0;

        LandingServiceItem::create([
            'type' => $request->type,
            'icon_class' => $request->icon_class,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $maxOrder + 1
        ]);

        return redirect()->back()->with('success', 'Servicio agregado correctamente.');
    }

    public function updateServiceItem(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:commercial,residential',
            'icon_class' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $serviceItem = LandingServiceItem::findOrFail($id);
        $serviceItem->update($request->all());

        return redirect()->back()->with('success', 'Servicio actualizado correctamente.');
    }

    public function deleteServiceItem($id)
    {
        $serviceItem = LandingServiceItem::findOrFail($id);
        $serviceItem->delete();

        return redirect()->back()->with('success', 'Servicio eliminado correctamente.');
    }

    public function storeServiceImage(Request $request)
    {
        $request->validate([
            'type' => 'required|in:commercial,residential',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'alt_text' => 'nullable|string|max:255'
        ]);

        if ($request->hasFile('image')) {
            // Crear directorio si no existe
            $uploadPath = public_path('images/services');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generar nombre único para la imagen
            $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();

            // Mover la imagen al directorio público
            $request->file('image')->move($uploadPath, $fileName);

            $maxOrder = LandingServiceImage::max('order') ?? 0;

            LandingServiceImage::create([
                'type' => $request->type,
                'image_path' => 'images/services/' . $fileName,
                'alt_text' => $request->alt_text,
                'order' => $maxOrder + 1
            ]);
        }

        return redirect()->back()->with('success', 'Imagen de servicio agregada correctamente.');
    }

    public function deleteServiceImage($id)
    {
        $image = LandingServiceImage::findOrFail($id);

        // Eliminar archivo físico del directorio público
        $filePath = public_path($image->image_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $image->delete();

        return redirect()->back()->with('success', 'Imagen de servicio eliminada correctamente.');
    }

    // ========================================
    // TESTIMONIALS METHODS
    // ========================================

    public function storeTestimonial(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_location' => 'nullable|string|max:255',
            'testimonial_text' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $data = $request->except('client_image');
        $maxOrder = LandingTestimonial::max('order') ?? 0;
        $data['order'] = $maxOrder + 1;

        // Manejar subida de imagen
        if ($request->hasFile('client_image')) {
            // Crear directorio si no existe
            $uploadPath = public_path('images/testimonials');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generar nombre único para la imagen
            $fileName = time() . '_' . uniqid() . '.' . $request->file('client_image')->getClientOriginalExtension();

            // Mover la imagen al directorio público
            $request->file('client_image')->move($uploadPath, $fileName);

            $data['client_image_path'] = 'images/testimonials/' . $fileName;
        }

        LandingTestimonial::create($data);

        return redirect()->back()->with('success', 'Testimonio agregado correctamente.');
    }

    public function updateTestimonial(Request $request, $id)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_location' => 'nullable|string|max:255',
            'testimonial_text' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $testimonial = LandingTestimonial::findOrFail($id);
        $data = $request->except('client_image');

        // Manejar subida de imagen
        if ($request->hasFile('client_image')) {
            // Eliminar imagen anterior si existe
            if ($testimonial->client_image_path) {
                $oldImagePath = public_path($testimonial->client_image_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Crear directorio si no existe
            $uploadPath = public_path('images/testimonials');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generar nombre único para la imagen
            $fileName = time() . '_' . uniqid() . '.' . $request->file('client_image')->getClientOriginalExtension();

            // Mover la imagen al directorio público
            $request->file('client_image')->move($uploadPath, $fileName);

            $data['client_image_path'] = 'images/testimonials/' . $fileName;
        }

        $testimonial->update($data);

        return redirect()->back()->with('success', 'Testimonio actualizado correctamente.');
    }

    public function deleteTestimonial($id)
    {
        $testimonial = LandingTestimonial::findOrFail($id);

        // Eliminar imagen si existe
        if ($testimonial->client_image_path) {
            $imagePath = public_path($testimonial->client_image_path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $testimonial->delete();

        return redirect()->back()->with('success', 'Testimonio eliminado correctamente.');
    }

    // ========================================
    // ABOUT VALUES METHODS
    // ========================================

    public function storeAboutValue(Request $request)
    {
        $request->validate([
            'icon_class' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $maxOrder = LandingAboutValue::max('order') ?? 0;

        LandingAboutValue::create([
            'icon_class' => $request->icon_class,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $maxOrder + 1
        ]);

        return redirect()->back()->with('success', 'Valor agregado correctamente.');
    }

    public function updateAboutValue(Request $request, $id)
    {
        $request->validate([
            'icon_class' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $aboutValue = LandingAboutValue::findOrFail($id);
        $aboutValue->update($request->all());

        return redirect()->back()->with('success', 'Valor actualizado correctamente.');
    }

    public function deleteAboutValue($id)
    {
        $aboutValue = LandingAboutValue::findOrFail($id);
        $aboutValue->delete();

        return redirect()->back()->with('success', 'Valor eliminado correctamente.');
    }
}
