using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using APIAnimalTherapy.Models;
using APIAnimalTherapy.Dtos;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace APIAnimalTherapy.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class ClienteController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public ClienteController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Cliente
        [HttpGet]
        public async Task<ActionResult<IEnumerable<Cliente>>> GetClientes()
        {
            return await _context.Clientes
                .Include(c => c.user) // Incluir la información del usuario relacionado
                .ToListAsync();
        }

        // GET: api/Cliente/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Cliente>> GetCliente(int id)
        {
            var cliente = await _context.Clientes
                .Include(c => c.user) // Incluir la información del usuario relacionado
                .FirstOrDefaultAsync(c => c.cliente_id == id);

            if (cliente == null)
            {
                return NotFound();
            }

            return cliente;
        }

        // GET: api/Cliente/GetClienteWithUser/{id}
        [HttpGet("GetClienteWithUser/{id}")]
        public async Task<ActionResult<ClienteWithUserDTO>> GetClienteWithUser(int id)
        {
            var cliente = await _context.Clientes
                .Include(c => c.user)
                .FirstOrDefaultAsync(c => c.cliente_id == id);

            if (cliente == null)
            {
                return NotFound(new { Message = "Cliente o usuario asociado no encontrado." });
            }

            var clienteWithUserDto = new ClienteWithUserDTO
            {
                ClienteId = cliente.cliente_id,
                UsuarioId = cliente.User_usuario_id,
                Nombre = cliente.user.nombre,
                Apellido = cliente.user.apellido,
                Correo = cliente.user.correo,
                Telefono = cliente.telefono,
                Direccion = cliente.direccion
            };

            return Ok(clienteWithUserDto);
        }

        // NUEVO: Verificar si el cliente tiene un animal asignado
        [HttpGet("HasAssignedAnimal/{clienteId}")]
        public async Task<IActionResult> HasAssignedAnimal(int clienteId)
        {
            var cliente = await _context.Clientes
                .Include(c => c.terapias) // Incluir terapias del cliente
                .ThenInclude(t => t.animal) // Incluir el animal asignado a la terapia
                .FirstOrDefaultAsync(c => c.cliente_id == clienteId);

            if (cliente == null)
            {
                return NotFound(new { Message = "Cliente no encontrado." });
            }

            // Verificar si alguna de las terapias tiene un animal asignado
            var hasAnimalAssigned = cliente.terapias.Any(t => t.animal != null);

            return Ok(new { HasAnimal = hasAnimalAssigned });
        }

        // POST: api/Cliente
        [HttpPost]
        public async Task<ActionResult<Cliente>> CreateCliente(Cliente cliente)
        {
            _context.Clientes.Add(cliente);
            await _context.SaveChangesAsync();

            return CreatedAtAction(nameof(GetCliente), new { id = cliente.cliente_id }, cliente);
        }

        // PUT: api/Cliente/5
        [HttpPut("{id}")]
        public async Task<IActionResult> UpdateCliente(int id, Cliente cliente)
        {
            if (id != cliente.cliente_id)
            {
                return BadRequest();
            }

            _context.Entry(cliente).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!ClienteExists(id))
                {
                    return NotFound();
                }
                else
                {
                    throw;
                }
            }

            return NoContent();
        }

        // PUT: api/Cliente/UpdatePreference
        [HttpPut("UpdatePreference")]
        public async Task<IActionResult> UpdatePreference([FromQuery] int clienteId, [FromBody] RequestPreference preference)
        {
            var cliente = await _context.Clientes.FirstOrDefaultAsync(c => c.cliente_id == clienteId);

            if (cliente == null)
            {
                return NotFound(new { Message = "Cliente no encontrado." });
            }

            cliente.Preferencia_animal = preference.preferencia;

            try
            {
                await _context.SaveChangesAsync();
                return Ok(new { Message = "Preferencia actualizada correctamente." });
            }
            catch (Exception ex)
            {
                return StatusCode(500, $"Error al actualizar la preferencia: {ex.Message}");
            }
        }

        // DELETE: api/Cliente/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteCliente(int id)
        {
            var cliente = await _context.Clientes.FindAsync(id);
            if (cliente == null)
            {
                return NotFound();
            }

            _context.Clientes.Remove(cliente);
            await _context.SaveChangesAsync();

            return NoContent();
        }

        private bool ClienteExists(int id)
        {
            return _context.Clientes.Any(e => e.cliente_id == id);
        }
    }


    public class RequestPreference
    {
        public string preferencia { get; set; }
    }
}
