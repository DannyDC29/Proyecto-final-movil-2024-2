using Microsoft.Maui.Controls;
using Newtonsoft.Json;
using System.Collections.ObjectModel;
using System.Linq;
using System.Threading.Tasks;
using therapy.Dtos;
using therapy.Services;

namespace therapy.pages
{
    public partial class Mascotas : ContentPage
    {
        private readonly ApiService _apiService;
        public ObservableCollection<AnimalDTO> ListaDeMascotas { get; set; }

        public Mascotas()
        {
            InitializeComponent();
            _apiService = new ApiService();
            ListaDeMascotas = new ObservableCollection<AnimalDTO>();
            BindingContext = this;
        }

        protected override async void OnAppearing()
        {
            base.OnAppearing();
            await LoadMascotas();
        }

        private async Task LoadMascotas()
        {
            try
            {
                string? clienteId = await SecureStorage.GetAsync("usuario_id");


                if (string.IsNullOrEmpty(clienteId) || clienteId.Equals("0"))
                {
                    await DisplayAlert("Error", "No se pudo obtener el cliente.", "OK");
                    return;
                }

                var animales = await _apiService.GetFilteredAnimalsAsync(Convert.ToInt32(clienteId));
                if (animales != null && animales.Any())
                {
                    ListaDeMascotas.Clear();
                    foreach (var animal in animales)
                    {
                        ListaDeMascotas.Add(animal);
                    }
                }
                else
                {
                    await DisplayAlert("Sin Resultados", "No se encontraron animales disponibles.", "OK");
                }
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Hubo un problema al cargar las mascotas: {ex.Message}", "OK");
            }
        }

        private async void OnMascotaSelected(object sender, SelectionChangedEventArgs e)
        {
            if (e.CurrentSelection.FirstOrDefault() is AnimalDTO selectedMascota)
            {
                await Navigation.PushAsync(new PerfilMascota(selectedMascota));
                ((CollectionView)sender).SelectedItem = null;
            }
        }
    }
}
