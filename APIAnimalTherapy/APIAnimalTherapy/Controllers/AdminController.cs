using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using APIAnimalTherapy.Models;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using APIAnimalTherapy.Dtos;

namespace APIAnimalTherapy.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class AdminController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public AdminController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Admin
        [HttpGet]
        public async Task<ActionResult<IEnumerable<Admin>>> GetAdmins()
        {
            return _context.Admins.ToList();
        }

        // GET: api/Admin/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Admin>> GetAdmin(int id)
        {
            var admin =  _context.Admins.FirstOrDefault(a => a.admin_id == id);

            if (admin == null)
            {
                return NotFound();
            }

            return admin;
        }

        // POST: api/Admin
        [HttpPost]
        public async Task<ActionResult<Admin>> CreateAdmin(Admin admin)
        {
            _context.Admins.Add(admin);
            await _context.SaveChangesAsync();

            return CreatedAtAction(nameof(GetAdmin), new { id = admin.admin_id }, admin);
        }

        // PUT: api/Admin/5
        [HttpPut("{id}")]
        public async Task<IActionResult> UpdateAdmin(int id, Admin admin)
        {
            if (id != admin.admin_id)
            {
                return BadRequest();
            }

            _context.Entry(admin).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!_context.Admins.Any(e => e.admin_id == id))
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

        // DELETE: api/Admin/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteAdmin(int id)
        {
            var admin = await _context.Admins.FindAsync(id);
            if (admin == null)
            {
                return NotFound();
            }

            _context.Admins.Remove(admin);
            await _context.SaveChangesAsync();

            return NoContent();
        }
    }
}
