using Microsoft.Maui;

namespace therapy
{
    public partial class App : Application
    {
        public static IServiceProvider Services { get; private set; }

        public App(IServiceProvider services)
        {
            InitializeComponent();

            Services = services;

            MainPage = new NavigationPage(new MainPage());
        }
    }
}

