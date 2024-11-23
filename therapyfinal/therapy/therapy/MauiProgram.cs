using Microsoft.Extensions.Logging;
using Plugin.Maui.Audio; // Plugin para el audio
using therapy.pages; // Páginas registradas
using therapy.Controllers; // Controladores registrados

namespace therapy
{
    public static class MauiProgram
    {
        public static MauiApp CreateMauiApp()
        {
            var builder = MauiApp.CreateBuilder();
            builder
                .UseMauiApp<App>()
                .ConfigureFonts(fonts =>
                {
                    fonts.AddFont("OpenSans-Regular.ttf", "OpenSansRegular");
                    fonts.AddFont("OpenSans-Semibold.ttf", "OpenSansSemibold");
                    fonts.AddFont("Baloo-Regular.ttf", "Baloo");
                });

            // Registro de IAudioManager como un singleton
            builder.Services.AddSingleton<Plugin.Maui.Audio.IAudioManager>(Plugin.Maui.Audio.AudioManager.Current);

            // Registro de páginas
            builder.Services.AddTransient<Preferencia>();
            builder.Services.AddTransient<Comenzar>();
            builder.Services.AddTransient<Login>();
            builder.Services.AddTransient<DatosPersonales>();
            builder.Services.AddTransient<Seguimiento>();
            builder.Services.AddTransient<Mascotas>();
            builder.Services.AddTransient<Perfil>();

            // Registro de HttpClient para UserController
            builder.Services.AddHttpClient<UserController>(client =>
            {
                client.BaseAddress = new Uri("http://localhost:3000"); // Reemplaza con tu URL base
            });

            // Configurar HttpClient para TherapyController
            builder.Services.AddHttpClient<TherapyController>(client =>
            {
                client.BaseAddress = new Uri("http://localhost:3000");
            });

            // Configurar HttpClient para AdminController
            builder.Services.AddHttpClient<AdminController>(client =>
            {
                client.BaseAddress = new Uri("http://localhost:3000");
            });

            // Configurar HttpClient para MascotasController
            builder.Services.AddHttpClient<MascotasController>(client =>
            {
                client.BaseAddress = new Uri("http://localhost:3000");
            });

            // Configurar HttpClient para ClientController
            builder.Services.AddHttpClient<ClientController>(client =>
            {
                client.BaseAddress = new Uri("http://localhost:3000");
            });

            // Configurar HttpClient para SpecialistController
            builder.Services.AddHttpClient<SpecialistController>(client =>
            {
                client.BaseAddress = new Uri("http://localhost:3000");
            });

            // Configurar HttpClient para FollowupController
            builder.Services.AddHttpClient<FollowupController>(client =>
            {
                client.BaseAddress = new Uri("http://localhost:3000");
            });

            // Configurar HttpClient para LogController
            builder.Services.AddHttpClient<LogController>(client =>
            {
                client.BaseAddress = new Uri("http://localhost:3000");
            });

#if DEBUG
            builder.Logging.AddDebug();
#endif

            return builder.Build();
        }
    }
}
