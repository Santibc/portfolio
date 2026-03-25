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
use App\Models\LandingHomeConfig;
use App\Models\LandingHeroValue;
use App\Models\LandingTestimonial;
use App\Models\LandingGalleryImage;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\Page;
use App\Models\Seo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
        $homeConfig = LandingHomeConfig::first();
        $heroValues = LandingHeroValue::orderBy('order')->get();
        $testimonials = LandingTestimonial::orderBy('order')->get();
        $galleryImages = LandingGalleryImage::orderBy('order')->get();
        $blogPosts = BlogPost::with('category', 'author')->latest()->get();
        $blogCategories = BlogCategory::withCount('posts')->get();

        $this->ensureLandingPagesExist();
        $pages = Page::where('page_type', 'landing')->get();
        $seoConfigs = Seo::with('page')->get();

        return view('admin.landing.index', compact(
            'config', 'services', 'contactInfo', 'about', 'layoutConfig',
            'pages', 'seoConfigs', 'homeConfig', 'heroValues', 'testimonials',
            'galleryImages', 'blogPosts', 'blogCategories'
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

        return redirect()->back()->with('success', 'Configuracion actualizada correctamente.');
    }

    // ========== CAROUSEL ==========
    public function storeCarouselImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'alt_text' => 'nullable|string|max:255'
        ]);

        if ($request->hasFile('image')) {
            $uploadPath = public_path('images/carousel');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);

            $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
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
        $filePath = public_path($image->image_path);
        if (file_exists($filePath)) unlink($filePath);
        $image->delete();

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }

    // ========== SERVICES ==========
    public function storeService(Request $request)
    {
        $request->validate([
            'icon_class' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'slug' => 'nullable|string|max:255|unique:landing_services,slug',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'featured_image_alt' => 'nullable|string|max:255',
        ]);

        $data = $request->except(['image']);
        $data['slug'] = $request->slug ?: Str::slug($request->title);
        $data['order'] = (LandingService::max('order') ?? 0) + 1;
        $data['is_active'] = true;

        if ($request->hasFile('image')) {
            $uploadPath = public_path('images/services');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
            $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move($uploadPath, $fileName);
            $data['image_path'] = 'images/services/' . $fileName;
        }

        // Create associated page for SEO
        $page = Page::create([
            'name' => $request->title,
            'slug' => 'servicio-' . $data['slug'],
            'url_path' => '/servicios/' . $data['slug'],
            'page_type' => 'service',
        ]);
        $data['page_id'] = $page->id;

        LandingService::create($data);

        return redirect()->back()->with('success', 'Servicio agregado correctamente.');
    }

    public function updateService(Request $request, $id)
    {
        $service = LandingService::findOrFail($id);

        $request->validate([
            'icon_class' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'slug' => 'nullable|string|max:255|unique:landing_services,slug,' . $id,
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'featured_image_alt' => 'nullable|string|max:255',
        ]);

        $data = $request->except(['image']);
        if ($request->slug) {
            $data['slug'] = $request->slug;
        }

        if ($request->hasFile('image')) {
            if ($service->image_path) {
                $oldPath = public_path($service->image_path);
                if (file_exists($oldPath)) unlink($oldPath);
            }
            $uploadPath = public_path('images/services');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
            $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move($uploadPath, $fileName);
            $data['image_path'] = 'images/services/' . $fileName;
        }

        $service->update($data);

        return redirect()->back()->with('success', 'Servicio actualizado correctamente.');
    }

    public function deleteService($id)
    {
        $service = LandingService::findOrFail($id);
        if ($service->image_path) {
            $path = public_path($service->image_path);
            if (file_exists($path)) unlink($path);
        }
        if ($service->page_id) {
            Seo::where('page_id', $service->page_id)->delete();
            Page::where('id', $service->page_id)->delete();
        }
        $service->delete();

        return redirect()->back()->with('success', 'Servicio eliminado correctamente.');
    }

    // ========== STEPS ==========
    public function storeStep(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255', 'description' => 'required|string']);
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
        $request->validate(['title' => 'required|string|max:255', 'description' => 'required|string']);
        LandingStep::findOrFail($id)->update($request->all());
        return redirect()->back()->with('success', 'Paso actualizado correctamente.');
    }

    public function deleteStep($id)
    {
        $step = LandingStep::findOrFail($id);
        $stepNumber = $step->step_number;
        $step->delete();
        LandingStep::where('step_number', '>', $stepNumber)->decrement('step_number');
        return redirect()->back()->with('success', 'Paso eliminado correctamente.');
    }

    // ========== CONTACT ==========
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

        return redirect()->back()->with('success', 'Informacion de contacto actualizada correctamente.');
    }

    public function sendContactEmail(Request $request)
    {
        \Log::info('=== CONTACT FORM: Inicio de envío ===');
        \Log::info('Datos recibidos:', $request->only(['name', 'email', 'subject', 'message']));

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'subject' => 'nullable|string|max:255',
                'message' => 'required|string'
            ]);
            \Log::info('Validación pasada correctamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validación fallida:', $e->errors());
            return response()->json(['success' => false, 'error' => 'Datos inválidos: ' . implode(', ', array_map(fn($msgs) => implode(', ', $msgs), $e->errors()))], 422);
        }

        $contactInfo = LandingContactInfo::first();
        \Log::info('ContactInfo encontrado:', [
            'exists' => $contactInfo ? true : false,
            'receive_messages_email' => $contactInfo->receive_messages_email ?? 'NO CONFIGURADO',
        ]);

        if (!$contactInfo || !$contactInfo->receive_messages_email) {
            \Log::error('No hay email de destino configurado en landing_contact_info.receive_messages_email');
            return response()->json(['success' => false, 'error' => 'No hay email de destino configurado. Configure receive_messages_email en el panel de administración.']);
        }

        \Log::info('Configuración de correo:', [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username') ? 'CONFIGURADO' : 'NO CONFIGURADO',
            'MAIL_PASSWORD' => config('mail.mailers.smtp.password') ? 'CONFIGURADO' : 'NO CONFIGURADO',
            'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
        ]);

        try {
            \Log::info('Intentando enviar email a: ' . $contactInfo->receive_messages_email);
            Mail::to($contactInfo->receive_messages_email)->send(new ContactFormMail($request->all()));
            \Log::info('=== CONTACT FORM: Email enviado exitosamente ===');
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('=== CONTACT FORM: Error al enviar email ===');
            \Log::error('Excepción: ' . get_class($e));
            \Log::error('Mensaje: ' . $e->getMessage());
            \Log::error('Archivo: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'error' => 'Error al enviar: ' . $e->getMessage()]);
        }
    }

    // ========== ABOUT ==========
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
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        $about = LandingAbout::first();
        $data = $request->except('main_image');

        if ($request->hasFile('main_image')) {
            if ($about && $about->main_image_path) {
                $oldPath = public_path($about->main_image_path);
                if (file_exists($oldPath)) unlink($oldPath);
            }
            $uploadPath = public_path('images/about');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
            $fileName = time() . '_' . uniqid() . '.' . $request->file('main_image')->getClientOriginalExtension();
            $request->file('main_image')->move($uploadPath, $fileName);
            $data['main_image_path'] = 'images/about/' . $fileName;
        }

        if ($about) { $about->update($data); } else { LandingAbout::create($data); }

        return redirect()->back()->with('success', 'Pagina Nosotros actualizada correctamente.');
    }

    // ========== TEAM ==========
    public function storeTeamMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'twitter_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url'
        ]);

        $data = $request->except('image');
        $data['order'] = (LandingTeamMember::max('order') ?? 0) + 1;

        if ($request->hasFile('image')) {
            $uploadPath = public_path('images/team');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
            $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'twitter_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url'
        ]);

        $member = LandingTeamMember::findOrFail($id);
        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($member->image_path) {
                $oldPath = public_path($member->image_path);
                if (file_exists($oldPath)) unlink($oldPath);
            }
            $uploadPath = public_path('images/team');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
            $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move($uploadPath, $fileName);
            $data['image_path'] = 'images/team/' . $fileName;
        }

        $member->update($data);
        return redirect()->back()->with('success', 'Miembro del equipo actualizado correctamente.');
    }

    public function deleteTeamMember($id)
    {
        $member = LandingTeamMember::findOrFail($id);
        if ($member->image_path) {
            $path = public_path($member->image_path);
            if (file_exists($path)) unlink($path);
        }
        $member->delete();
        return redirect()->back()->with('success', 'Miembro del equipo eliminado correctamente.');
    }

    // ========== LAYOUT ==========
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
            'whatsapp_url' => 'nullable|string|max:255',
            'tiktok_url' => 'nullable|url',
            'footer_address' => 'required|string|max:255',
            'footer_city' => 'required|string|max:255',
            'footer_phone' => 'required|string|max:255',
            'footer_email' => 'required|email',
            'copyright_company' => 'required|string|max:255',
            'footer_description' => 'nullable|string',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120'
        ]);

        $layoutConfig = LandingLayoutConfig::first();
        $data = $request->except('footer_logo');

        if ($request->hasFile('footer_logo')) {
            if ($layoutConfig && $layoutConfig->footer_logo_path) {
                $oldPath = public_path($layoutConfig->footer_logo_path);
                if (file_exists($oldPath)) unlink($oldPath);
            }
            $uploadPath = public_path('images/layout');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
            $fileName = 'footer_logo_' . time() . '.' . $request->file('footer_logo')->getClientOriginalExtension();
            $request->file('footer_logo')->move($uploadPath, $fileName);
            $data['footer_logo_path'] = 'images/layout/' . $fileName;
        }

        if ($layoutConfig) { $layoutConfig->update($data); } else { LandingLayoutConfig::create($data); }

        return redirect()->back()->with('success', 'Configuracion del sitio actualizada correctamente.');
    }

    // ========== HOME CONFIG ==========
    public function updateHomeConfig(Request $request)
    {
        $request->validate([
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'required|string|max:255',
            'hero_description' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'hero_services_button_url' => 'nullable|string|max:255',
            'hero_estimate_button_url' => 'nullable|string|max:255',
            'about_title' => 'required|string|max:255',
            'about_lead' => 'nullable|string',
            'about_description' => 'nullable|string',
            'about_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
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

        if ($request->hasFile('hero_image')) {
            if ($homeConfig && $homeConfig->hero_image_path) {
                $oldPath = public_path($homeConfig->hero_image_path);
                if (file_exists($oldPath)) unlink($oldPath);
            }
            $uploadPath = public_path('images/home');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
            $fileName = 'hero_' . time() . '.' . $request->file('hero_image')->getClientOriginalExtension();
            $request->file('hero_image')->move($uploadPath, $fileName);
            $data['hero_image_path'] = 'images/home/' . $fileName;
        }

        if ($request->hasFile('about_image')) {
            if ($homeConfig && $homeConfig->about_image_path) {
                $oldPath = public_path($homeConfig->about_image_path);
                if (file_exists($oldPath)) unlink($oldPath);
            }
            $uploadPath = public_path('images/home');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
            $fileName = 'about_' . time() . '.' . $request->file('about_image')->getClientOriginalExtension();
            $request->file('about_image')->move($uploadPath, $fileName);
            $data['about_image_path'] = 'images/home/' . $fileName;
        }

        if ($homeConfig) { $homeConfig->update($data); } else { LandingHomeConfig::create($data); }

        return redirect()->back()->with('success', 'Configuracion del Home actualizada correctamente.');
    }

    // ========== HERO VALUES ==========
    public function storeHeroValue(Request $request)
    {
        $request->validate(['icon_class' => 'required|string|max:255', 'title' => 'required|string|max:255']);
        LandingHeroValue::create([
            'icon_class' => $request->icon_class,
            'title' => $request->title,
            'order' => (LandingHeroValue::max('order') ?? 0) + 1,
            'is_active' => true
        ]);
        return redirect()->back()->with('success', 'Hero value agregado correctamente.');
    }

    public function updateHeroValue(Request $request, $id)
    {
        $request->validate(['icon_class' => 'required|string|max:255', 'title' => 'required|string|max:255']);
        LandingHeroValue::findOrFail($id)->update($request->all());
        return redirect()->back()->with('success', 'Hero value actualizado correctamente.');
    }

    public function deleteHeroValue($id)
    {
        LandingHeroValue::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Hero value eliminado correctamente.');
    }

    // ========== TESTIMONIALS ==========
    public function storeTestimonial(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_role' => 'nullable|string|max:255',
            'testimonial' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        LandingTestimonial::create([
            'client_name' => $request->client_name,
            'client_role' => $request->client_role,
            'testimonial' => $request->testimonial,
            'rating' => $request->rating,
            'order' => (LandingTestimonial::max('order') ?? 0) + 1,
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

        LandingTestimonial::findOrFail($id)->update($request->only(['client_name', 'client_role', 'testimonial', 'rating']));
        return redirect()->back()->with('success', 'Testimonio actualizado correctamente.');
    }

    public function deleteTestimonial($id)
    {
        LandingTestimonial::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Testimonio eliminado correctamente.');
    }

    // ========== SEO ==========
    private function ensureLandingPagesExist()
    {
        $landingPages = [
            ['name' => 'Inicio', 'slug' => 'home', 'url_path' => '/'],
            ['name' => 'Nosotros', 'slug' => 'nosotros', 'url_path' => '/nosotros'],
            ['name' => 'Servicios', 'slug' => 'servicios', 'url_path' => '/servicios'],
            ['name' => 'Contacto', 'slug' => 'contacto', 'url_path' => '/contacto'],
            ['name' => 'Blog', 'slug' => 'blog', 'url_path' => '/blog'],
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
            'is_active' => 'boolean',
            'og_title' => 'nullable|string|max:150',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'og_type' => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'page_id', 'meta_title', 'meta_description', 'meta_keywords',
            'canonical_url', 'robots', 'focus_keyword', 'og_title', 'og_description', 'og_type'
        ]);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('og_image')) {
            $uploadPath = public_path('images/seo');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
            $fileName = 'og_' . time() . '.' . $request->file('og_image')->getClientOriginalExtension();
            $request->file('og_image')->move($uploadPath, $fileName);
            $data['og_image'] = 'images/seo/' . $fileName;
        }

        $seo = Seo::where('page_id', $request->page_id)->first();
        if ($seo) { $seo->update($data); } else { Seo::create($data); }

        return redirect()->back()->with('success', 'Configuracion SEO actualizada correctamente.');
    }

    public function getSeoData($pageId)
    {
        return response()->json(Seo::where('page_id', $pageId)->first());
    }

    public function deleteSeo($id)
    {
        Seo::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Configuracion SEO eliminada correctamente.');
    }

    // ========== BLOG ==========
    public function storeBlogPost(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string',
            'body' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'featured_image_alt' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string',
        ]);

        $data = $request->except(['featured_image', 'tags', 'meta_title', 'meta_description']);
        $data['slug'] = $request->slug ?: Str::slug($request->title);
        $data['author_id'] = auth()->id();

        if ($request->status === 'published' && !$request->published_at) {
            $data['published_at'] = now();
        }

        // Calculate reading time
        $data['reading_time'] = max(1, (int) ceil(str_word_count(strip_tags($request->body)) / 200));

        if ($request->hasFile('featured_image')) {
            $uploadPath = public_path('images/blog');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
            $fileName = time() . '_' . uniqid() . '.' . $request->file('featured_image')->getClientOriginalExtension();
            $request->file('featured_image')->move($uploadPath, $fileName);
            $data['featured_image'] = 'images/blog/' . $fileName;
        }

        // Create page for SEO
        $page = Page::create([
            'name' => $request->title,
            'slug' => 'blog-' . $data['slug'],
            'url_path' => '/blog/' . $data['slug'],
            'page_type' => 'blog',
        ]);
        $data['page_id'] = $page->id;

        $post = BlogPost::create($data);

        // Handle SEO
        if ($request->meta_title || $request->meta_description) {
            Seo::create([
                'page_id' => $page->id,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'robots' => 'index,follow',
                'is_active' => true,
            ]);
        }

        // Handle tags
        if ($request->tags) {
            $tagNames = array_map('trim', explode(',', $request->tags));
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) {
                    $tag = BlogTag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName]
                    );
                    $tagIds[] = $tag->id;
                }
            }
            $post->tags()->sync($tagIds);
        }

        return redirect()->back()->with('success', 'Articulo creado correctamente.');
    }

    public function updateBlogPost(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $id,
            'excerpt' => 'nullable|string',
            'body' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'featured_image_alt' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:150',
            'meta_description' => 'nullable|string',
        ]);

        $data = $request->except(['featured_image', 'tags', 'meta_title', 'meta_description']);
        if ($request->slug) $data['slug'] = $request->slug;

        if ($request->status === 'published' && !$post->published_at && !$request->published_at) {
            $data['published_at'] = now();
        }

        $data['reading_time'] = max(1, (int) ceil(str_word_count(strip_tags($request->body)) / 200));

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                $oldPath = public_path($post->featured_image);
                if (file_exists($oldPath)) unlink($oldPath);
            }
            $uploadPath = public_path('images/blog');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
            $fileName = time() . '_' . uniqid() . '.' . $request->file('featured_image')->getClientOriginalExtension();
            $request->file('featured_image')->move($uploadPath, $fileName);
            $data['featured_image'] = 'images/blog/' . $fileName;
        }

        $post->update($data);

        // Update SEO
        if ($post->page_id && ($request->meta_title || $request->meta_description)) {
            Seo::updateOrCreate(
                ['page_id' => $post->page_id],
                [
                    'meta_title' => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'robots' => 'index,follow',
                    'is_active' => true,
                ]
            );
        }

        // Handle tags
        if ($request->has('tags')) {
            $tagNames = array_map('trim', explode(',', $request->tags ?? ''));
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) {
                    $tag = BlogTag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName]
                    );
                    $tagIds[] = $tag->id;
                }
            }
            $post->tags()->sync($tagIds);
        }

        return redirect()->back()->with('success', 'Articulo actualizado correctamente.');
    }

    public function deleteBlogPost($id)
    {
        $post = BlogPost::findOrFail($id);
        if ($post->featured_image) {
            $path = public_path($post->featured_image);
            if (file_exists($path)) unlink($path);
        }
        if ($post->page_id) {
            Seo::where('page_id', $post->page_id)->delete();
            Page::where('id', $post->page_id)->delete();
        }
        $post->tags()->detach();
        $post->delete();

        return redirect()->back()->with('success', 'Articulo eliminado correctamente.');
    }

    public function storeBlogCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        BlogCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Categoria creada correctamente.');
    }

    public function updateBlogCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = BlogCategory::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Categoria actualizada correctamente.');
    }

    public function deleteBlogCategory($id)
    {
        $category = BlogCategory::findOrFail($id);
        BlogPost::where('category_id', $id)->update(['category_id' => null]);
        $category->delete();

        return redirect()->back()->with('success', 'Categoria eliminada correctamente.');
    }

    // ========== GALLERY ==========
    public function storeGalleryImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('image')) {
            $uploadPath = public_path('images/gallery');
            if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
            $fileName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move($uploadPath, $fileName);

            LandingGalleryImage::create([
                'image_path' => 'images/gallery/' . $fileName,
                'alt_text' => $request->alt_text,
                'caption' => $request->caption,
                'category' => $request->category,
                'order' => (LandingGalleryImage::max('order') ?? 0) + 1,
                'is_active' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Imagen de galeria agregada correctamente.');
    }

    public function updateGalleryImage(Request $request, $id)
    {
        $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        $image = LandingGalleryImage::findOrFail($id);
        $image->update($request->only(['alt_text', 'caption', 'category']));

        return redirect()->back()->with('success', 'Imagen actualizada correctamente.');
    }

    public function deleteGalleryImage($id)
    {
        $image = LandingGalleryImage::findOrFail($id);
        $path = public_path($image->image_path);
        if (file_exists($path)) unlink($path);
        $image->delete();

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }
}
