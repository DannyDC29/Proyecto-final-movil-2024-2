namespace therapy;

public partial class MainPage : ContentPage
{
    public MainPage()
    {
        InitializeComponent();
        ShowLoginAfterDelay();
    }

    private async void ShowLoginAfterDelay()
    {
        // Espera 3 segundos
        await Task.Delay(3000);

        // Navega a la página de Login y elimina MainPage de la pila de navegación
        await Navigation.PushAsync(new pages.Login());
        Navigation.RemovePage(this);
    }
}
