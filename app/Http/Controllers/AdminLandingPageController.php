<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LandingConfiguracion;
use App\Models\LandingCarouselImage;
use App\Models\LandingService;
use App\Models\LandingStep;
use App\Models\LandingContactInfo;
use App\Models\LandingAbout;
use App\Models\LandingTeamMember;
use App\Models\LandingLayoutConfig;
use App\Models\Page;
use App\Models\Seo;
use App\Models\LandingPricingConfig;
use App\Models\LandingPricingRange;
use App\Models\LandingHomeConfig;
use App\Models\LandingHeroValue;
use App\Models\LandingTestimonial;
use App\Models\ServiceExtra;
use App\Models\RoomTypePrice;
use App\Models\CleanerHourPrice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Mail\ContactFormMail;

class AdminLandingPageController extends Controller
{
    public function index()
    {
        $config = LandingConfiguracion::first();
        $services = LandingService::orderBy('order')->get();
        $contactInfo = LandingContactInfo::first();
        $about = LandingAbout::first();
        $layoutConfig = LandingLayoutConfig::first();
        $pricingConfig = LandingPricingConfig::first();
        $pricingRanges = LandingPricingRange::orderBy('order')->get();
        $homeConfig = LandingHomeConfig::first();
        $heroValues = LandingHeroValue::orderBy('order')->get();
        $testimonials = LandingTestimonial::orderBy('order')->get();
        $serviceExtras = ServiceExtra::orderBy('order')->get();
        $roomTypePrices = RoomTypePrice::orderBy('order')->get();
        $cleanerHourPrices = CleanerHourPrice::orderBy('num_cleaners')->orderBy('num_hours')->get();

        // Get or create landing pages
        $this->ensureLandingPagesExist();
        $pages = Page::where('page_type', 'landing')->get();
        $seoConfigs = Seo::with('page')->get();

        return view('admin.landing.index', compact(
            'config', 'services', 'contactInfo',
            'about', 'layoutConfig', 'pages', 'seoConfigs',
            'pricingConfig', 'pricingRanges', 'homeConfig', 'heroValues', 'testimonials',
            'serviceExtras', 'roomTypePrices', 'cleanerHourPrices'
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
    
    public function storeCarouselImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alt_text' => 'nullable|string|max:255'
        ]);
        
        if ($request->hasFile('image')) {
            // Crear directorio si no existe
            $uploadPath = public_path('images/carousel');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Generar nombre único para la imagen
            $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            
            // Mover la imagen al directorio público
            $request->file('image')->move($uploadPath, $fileName);
            
            $maxOrder = LandingCarouselImage::max('order') ?? 0;
            
            LandingCarouselImage::create([
                'image_path' => 'images/carousel/' . $fileName,
                'alt_text' => $request->alt_text,
                'order' => $maxOrder + 1
            ]);
        }
        
        return redirect()->back()->with('success', 'Imagen agregada correctamente.');
    }
    
    public function deleteCarouselImage($id)
    {
        $image = LandingCarouselImage::findOrFail($id);
        
        // Eliminar archivo físico del directorio público
        $filePath = public_path($image->image_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $image->delete();
        
        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }
    
    public function storeService(Request $request)
    {
        $request->validate([
            'icon_class' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);
        
        $maxOrder = LandingService::max('order') ?? 0;
        
        LandingService::create([
            'icon_class' => $request->icon_class,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $maxOrder + 1
        ]);
        
        return redirect()->back()->with('success', 'Servicio agregado correctamente.');
    }
    
    public function updateService(Request $request, $id)
    {
        $request->validate([
            'icon_class' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);
        
        $service = LandingService::findOrFail($id);
        $service->update($request->all());
        
        return redirect()->back()->with('success', 'Servicio actualizado correctamente.');
    }
    
    public function deleteService($id)
    {
        $service = LandingService::findOrFail($id);
        $service->delete();
        
        return redirect()->back()->with('success', 'Servicio eliminado correctamente.');
    }
    
    public function storeStep(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);
        
        $maxOrder = LandingStep::max('order') ?? 0;
        $maxStepNumber = LandingStep::max('step_number') ?? 0;
        
        LandingStep::create([
            'title' => $request->title,
            'description' => $request->description,
            'step_number' => $maxStepNumber + 1,
            'order' => $maxOrder + 1
        ]);
        
        return redirect()->back()->with('success', 'Paso agregado correctamente.');
    }
    
    public function updateStep(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);
        
        $step = LandingStep::findOrFail($id);
        $step->update($request->all());
        
        return redirect()->back()->with('success', 'Paso actualizado correctamente.');
    }
    
    public function deleteStep($id)
    {
        $step = LandingStep::findOrFail($id);
        $stepNumber = $step->step_number;
        
        $step->delete();
        
        // Reorganizar números de pasos
        LandingStep::where('step_number', '>', $stepNumber)
                  ->decrement('step_number');
        
        return redirect()->back()->with('success', 'Paso eliminado correctamente.');
    }
    
    public function updateContactInfo(Request $request)
    {
        $request->validate([
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
            'page_subtitle' => 'nullable|string|max:255',
            'purpose_title' => 'required|string|max:255',
            'purpose_content' => 'required|string',
            'mission_title' => 'required|string|max:255',
            'mission_content' => 'required|string',
            'vision_title' => 'required|string|max:255',
            'vision_content' => 'required|string',
            'stats_years_experience' => 'required|integer|min:0',
            'stats_happy_clients' => 'required|integer|min:0',
            'stats_client_satisfaction' => 'required|integer|min:0|max:100',
            'value1_icon' => 'required|string|max:255',
            'value1_title' => 'required|string|max:255',
            'value1_description' => 'nullable|string',
            'value2_icon' => 'required|string|max:255',
            'value2_title' => 'required|string|max:255',
            'value2_description' => 'nullable|string',
            'value3_icon' => 'required|string|max:255',
            'value3_title' => 'required|string|max:255',
            'value3_description' => 'nullable|string',
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
    
    // Métodos para miembros del equipo
    public function storeTeamMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'twitter_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url'
        ]);
        
        $data = $request->except('image');
        $maxOrder = LandingTeamMember::max('order') ?? 0;
        $data['order'] = $maxOrder + 1;
        
        // Manejar subida de imagen
        if ($request->hasFile('image')) {
            // Crear directorio si no existe
            $uploadPath = public_path('images/team');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Generar nombre único para la imagen
            $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            
            // Mover la imagen al directorio público
            $request->file('image')->move($uploadPath, $fileName);
            
            $data['image_path'] = 'images/team/' . $fileName;
        }
        
        LandingTeamMember::create($data);
        
        return redirect()->back()->with('success', 'Miembro del equipo agregado correctamente.');
    }
    
    public function updateTeamMember(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'twitter_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url'
        ]);
        
        $member = LandingTeamMember::findOrFail($id);
        $data = $request->except('image');
        
        // Manejar subida de imagen
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($member->image_path) {
                $oldImagePath = public_path($member->image_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            // Crear directorio si no existe
            $uploadPath = public_path('images/team');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Generar nombre único para la imagen
            $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            
            // Mover la imagen al directorio público
            $request->file('image')->move($uploadPath, $fileName);
            
            $data['image_path'] = 'images/team/' . $fileName;
        }
        
        $member->update($data);
        
        return redirect()->back()->with('success', 'Miembro del equipo actualizado correctamente.');
    }
    
    public function deleteTeamMember($id)
    {
        $member = LandingTeamMember::findOrFail($id);
        
        // Eliminar imagen si existe
        if ($member->image_path) {
            $imagePath = public_path($member->image_path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $member->delete();
        
        return redirect()->back()->with('success', 'Miembro del equipo eliminado correctamente.');
    }
    
    // Métodos para configuración del layout
    public function updateLayoutConfig(Request $request)
    {
        $request->validate([
            'site_title' => 'required|string|max:255',
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
            'copyright_company' => 'required|string|max:255',
            'footer_description' => 'nullable|string',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $layoutConfig = LandingLayoutConfig::first();
        $data = $request->except('footer_logo');

        // Manejar subida de imagen del logo del footer
        if ($request->hasFile('footer_logo')) {
            // Eliminar imagen anterior si existe
            if ($layoutConfig && $layoutConfig->footer_logo_path) {
                $oldImagePath = public_path($layoutConfig->footer_logo_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Crear directorio si no existe
            $uploadPath = public_path('images/layout');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generar nombre único para la imagen
            $fileName = 'footer_logo_' . time() . '.' . $request->file('footer_logo')->getClientOriginalExtension();

            // Mover la imagen al directorio público
            $request->file('footer_logo')->move($uploadPath, $fileName);

            $data['footer_logo_path'] = 'images/layout/' . $fileName;
        }

        if ($layoutConfig) {
            $layoutConfig->update($data);
        } else {
            LandingLayoutConfig::create($data);
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

    public function updateHomeConfig(Request $request)
    {
        $request->validate([
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'required|string|max:255',
            'hero_description' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hero_services_button_url' => 'nullable|string|max:255',
            'hero_estimate_button_url' => 'nullable|string|max:255',
            'about_title' => 'required|string|max:255',
            'about_lead' => 'nullable|string',
            'about_description' => 'nullable|string',
            'about_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'about_years_experience' => 'required|integer|min:0',
            'about_happy_clients' => 'required|integer|min:0',
            'about_client_satisfaction' => 'required|integer|min:0|max:100',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
        ]);

        $homeConfig = LandingHomeConfig::first();
        $data = $request->except(['hero_image', 'about_image']);

        // Manejar subida de imagen hero
        if ($request->hasFile('hero_image')) {
            if ($homeConfig && $homeConfig->hero_image_path) {
                $oldImagePath = public_path($homeConfig->hero_image_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $uploadPath = public_path('images/home');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $fileName = 'hero_' . time() . '.' . $request->file('hero_image')->getClientOriginalExtension();
            $request->file('hero_image')->move($uploadPath, $fileName);
            $data['hero_image_path'] = 'images/home/' . $fileName;
        }

        // Manejar subida de imagen about
        if ($request->hasFile('about_image')) {
            if ($homeConfig && $homeConfig->about_image_path) {
                $oldImagePath = public_path($homeConfig->about_image_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $uploadPath = public_path('images/home');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $fileName = 'about_' . time() . '.' . $request->file('about_image')->getClientOriginalExtension();
            $request->file('about_image')->move($uploadPath, $fileName);
            $data['about_image_path'] = 'images/home/' . $fileName;
        }

        if ($homeConfig) {
            $homeConfig->update($data);
        } else {
            LandingHomeConfig::create($data);
        }

        return redirect()->back()->with('success', 'Configuración del Home actualizada correctamente.');
    }

    // Hero Values CRUD
    public function storeHeroValue(Request $request)
    {
        $request->validate([
            'icon_class' => 'required|string|max:255',
            'title' => 'required|string|max:255',
        ]);

        $maxOrder = LandingHeroValue::max('order') ?? 0;

        LandingHeroValue::create([
            'icon_class' => $request->icon_class,
            'title' => $request->title,
            'order' => $maxOrder + 1,
            'is_active' => true
        ]);

        return redirect()->back()->with('success', 'Hero value agregado correctamente.');
    }

    public function updateHeroValue(Request $request, $id)
    {
        $request->validate([
            'icon_class' => 'required|string|max:255',
            'title' => 'required|string|max:255',
        ]);

        $heroValue = LandingHeroValue::findOrFail($id);
        $heroValue->update($request->all());

        return redirect()->back()->with('success', 'Hero value actualizado correctamente.');
    }

    public function deleteHeroValue($id)
    {
        $heroValue = LandingHeroValue::findOrFail($id);
        $heroValue->delete();

        return redirect()->back()->with('success', 'Hero value eliminado correctamente.');
    }

    // Testimonials CRUD
    public function storeTestimonial(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_role' => 'nullable|string|max:255',
            'testimonial' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $maxOrder = LandingTestimonial::max('order') ?? 0;

        LandingTestimonial::create([
            'client_name' => $request->client_name,
            'client_role' => $request->client_role,
            'testimonial' => $request->testimonial,
            'rating' => $request->rating,
            'order' => $maxOrder + 1,
            'is_active' => true
        ]);

        return redirect()->back()->with('success', 'Testimonio agregado correctamente.');
    }

    public function updateTestimonial(Request $request, $id)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_role' => 'nullable|string|max:255',
            'testimonial' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $testimonial = LandingTestimonial::findOrFail($id);
        $testimonial->update($request->only(['client_name', 'client_role', 'testimonial', 'rating']));

        return redirect()->back()->with('success', 'Testimonio actualizado correctamente.');
    }

    public function deleteTestimonial($id)
    {
        $testimonial = LandingTestimonial::findOrFail($id);
        $testimonial->delete();

        return redirect()->back()->with('success', 'Testimonio eliminado correctamente.');
    }

    // Service Extras CRUD
    public function storeServiceExtra(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon_class' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $maxOrder = ServiceExtra::max('order') ?? 0;

        ServiceExtra::create([
            'name' => $request->name,
            'icon_class' => $request->icon_class,
            'price' => $request->price,
            'order' => $maxOrder + 1,
            'is_active' => true
        ]);

        return redirect()->back()->with('success', 'Extra agregado correctamente.');
    }

    public function updateServiceExtra(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon_class' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $extra = ServiceExtra::findOrFail($id);
        $extra->update($request->only(['name', 'icon_class', 'price']));

        return redirect()->back()->with('success', 'Extra actualizado correctamente.');
    }

    public function deleteServiceExtra($id)
    {
        $extra = ServiceExtra::findOrFail($id);
        $extra->delete();

        return redirect()->back()->with('success', 'Extra eliminado correctamente.');
    }

    // Room Type Prices CRUD
    public function updateRoomTypePrice(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        $roomType = RoomTypePrice::findOrFail($id);
        $roomType->update(['price' => $request->price]);

        return redirect()->back()->with('success', 'Precio actualizado correctamente.');
    }

    // Cleaner Hour Prices CRUD
    public function updateCleanerHourPrice(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
        ]);

        $price = CleanerHourPrice::findOrFail($id);
        $price->update(['price' => $request->price]);

        return redirect()->back()->with('success', 'Precio actualizado correctamente.');
    }

    // Base Pricing Configuration
    public function updateBasePricing(Request $request)
    {
        $request->validate([
            'cleaner_price' => 'required|numeric|min:0',
            'hour_price' => 'required|numeric|min:0',
            'normal_service_price' => 'required|numeric|min:0',
            'deep_service_price' => 'required|numeric|min:0',
        ]);

        $pricingConfig = LandingPricingConfig::first();

        if ($pricingConfig) {
            $pricingConfig->update([
                'cleaner_price' => $request->cleaner_price,
                'hour_price' => $request->hour_price,
                'normal_service_price' => $request->normal_service_price,
                'deep_service_price' => $request->deep_service_price,
            ]);
        } else {
            LandingPricingConfig::create([
                'cleaner_price' => $request->cleaner_price,
                'hour_price' => $request->hour_price,
                'normal_service_price' => $request->normal_service_price,
                'deep_service_price' => $request->deep_service_price,
            ]);
        }

        return redirect()->back()->with('success', 'Configuración de precios base actualizada correctamente.');
    }
}
