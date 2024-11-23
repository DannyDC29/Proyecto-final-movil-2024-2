using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using APIAnimalTherapy.Models;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using APIAnimalTherapy.Dtos;
using BCrypt.Net;  // Usando BCrypt para el hashing

namespace APIAnimalTherapy.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class UserController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public UserController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/User
        [HttpGet]
        public async Task<ActionResult<IEnumerable<User>>> GetUsers()
        {
            return await _context.Users.ToListAsync();
        }

        // GET: api/User/5
        [HttpGet("{id}")]
        public async Task<ActionResult<User>> GetUser(int id)
        {
            var user = await _context.Users.FindAsync(id);

            if (user == null)
            {
                return NotFound();
            }

            return user;
        }

        // POST: api/User
        [HttpPost]
        public async Task<ActionResult<User>> CreateUser(User user)
        {
            // Hashear la contraseña antes de guardarla
            user.contrasena = BCrypt.Net.BCrypt.HashPassword(user.contrasena);

            _context.Users.Add(user);
            await _context.SaveChangesAsync();

            return CreatedAtAction(nameof(GetUser), new { id = user.usuario_id }, user);
        }

        // PUT: api/User/5
        [HttpPut("{id}")]
        public async Task<IActionResult> UpdateUser(int id, User user)
        {
            if (id != user.usuario_id)
            {
                return BadRequest();
            }

            // Si la contraseña se está actualizando, hashearla
            if (!string.IsNullOrEmpty(user.contrasena))
            {
                user.contrasena = BCrypt.Net.BCrypt.HashPassword(user.contrasena);
            }

            _context.Entry(user).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!_context.Users.Any(e => e.usuario_id == id))
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

        // DELETE: api/User/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteUser(int id)
        {
            var user = await _context.Users.FindAsync(id);
            if (user == null)
            {
                return NotFound();
            }

            _context.Users.Remove(user);
            await _context.SaveChangesAsync();

            return NoContent();
        }

        // Authenticate: api/User/Authenticate
        [HttpPost("Authenticate")]
        public async Task<ActionResult> Authenticate(LoginDto loginDto)
        {
            // Validar que el correo exista en la tabla User
            var user = await _context.Users
                .FirstOrDefaultAsync(u => u.correo == loginDto.correo);

            if (user == null)
            {
                return Unauthorized("Correo o contraseña incorrectos.");
            }

            // Verificar que la contraseña coincida con el hash almacenado en la base de datos
            bool passwordMatches = BCrypt.Net.BCrypt.Verify(loginDto.contrasena, user.contrasena);

            if (!passwordMatches)
            {
                return Unauthorized("Correo o contraseña incorrectos.");
            }

            // Validar que el usuario esté relacionado con un cliente
            var cliente = await _context.Clientes
                .FirstOrDefaultAsync(c => c.User_usuario_id == user.usuario_id);

            if (cliente == null)
            {
                return Unauthorized("Este usuario no está autorizado para iniciar sesión.");
            }

            // Si las credenciales son correctas y el usuario está registrado como cliente, devolver información
            return Ok(new
            {
                user.usuario_id,
                user.correo,
                cliente.cliente_id, // O cualquier otro dato relevante de cliente que necesites
                message = "Inicio de sesión exitoso"
            });
        }
    }
}
